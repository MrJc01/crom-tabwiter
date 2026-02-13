<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Post;
use app\models\PostTag;
use app\models\User;
use Yii;
use GuzzleHttp\Client;

/**
 * Reaper controller: scheduled maintenance tasks.
 *
 * CRON setup:
 *   # Daily decay (every day at 3:00 AM)
 *   0 3 * * * php /path/to/yii reaper/decay
 *
 *   # Purge inactive guests (every 6 hours)
 *   0 */6 * * * php /path/to/yii reaper/purge-inactives
 *
 *   # Sync mana (every Monday at 00:00)
 *   0 0 * * 1 php /path/to/yii reaper/sync-mana
 */
class ReaperController extends Controller
{
    private string $logFile;

    public function init()
    {
        parent::init();
        $this->logFile = Yii::getAlias('@runtime/reaper.log');
    }

    /**
     * Apply decay to all local posts atomically.
     * Posts reaching the death threshold are deleted.
     */
    public function actionDecay()
    {
        $db = Yii::$app->db;
        $decay = Yii::$app->params['decayPerDay'] ?? 1;
        $threshold = Yii::$app->params['deathThreshold'] ?? -10;

        $transaction = $db->beginTransaction();
        try {
            // Atomic decay: subtract points from all local posts
            $updated = $db->createCommand(
                "UPDATE {{%post}} SET points = points - :decay WHERE is_tabnews_sync = 0",
                [':decay' => $decay]
            )->execute();

            // Delete dead posts and their tags
            $deadIds = $db->createCommand(
                "SELECT id FROM {{%post}} WHERE points <= :threshold",
                [':threshold' => $threshold]
            )->queryColumn();

            $deleted = 0;
            if (!empty($deadIds)) {
                PostTag::deleteAll(['post_id' => $deadIds]);
                $deleted = Post::deleteAll(['id' => $deadIds]);
            }

            $transaction->commit();

            $msg = date('Y-m-d H:i:s') . " [DECAY] Decayed: {$updated} posts, Deleted: {$deleted} dead posts.";
            $this->log($msg);
            $this->stdout($msg . "\n");

            return ExitCode::OK;
        } catch (\Exception $e) {
            $transaction->rollBack();
            $msg = date('Y-m-d H:i:s') . " [DECAY ERROR] " . $e->getMessage();
            $this->log($msg);
            $this->stderr($msg . "\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * Remove guest users who have been inactive for too long.
     */
    public function actionPurgeInactives()
    {
        $hours = Yii::$app->params['guestInactiveHours'] ?? 24;
        $threshold = time() - ($hours * 3600);

        // Delete posts from inactive guests first
        $inactiveUserIds = User::find()
            ->select('id')
            ->where(['is_validated' => false])
            ->andWhere(['<', 'last_active_at', $threshold])
            ->column();

        $deletedPosts = 0;
        $deletedTags = 0;
        if (!empty($inactiveUserIds)) {
            $postIds = Post::find()
                ->select('id')
                ->where(['user_id' => $inactiveUserIds])
                ->column();

            if (!empty($postIds)) {
                $deletedTags = PostTag::deleteAll(['post_id' => $postIds]);
                $deletedPosts = Post::deleteAll(['id' => $postIds]);
            }
        }

        $count = User::deleteAll([
            'and',
            ['is_validated' => false],
            ['<', 'last_active_at', $threshold],
        ]);

        $msg = date('Y-m-d H:i:s') . " [PURGE] Deleted: {$count} guests, {$deletedPosts} posts, {$deletedTags} tags.";
        $this->log($msg);
        $this->stdout($msg . "\n");

        return ExitCode::OK;
    }

    /**
     * Sync mana_weekly with TabCoins balance from TabNews API.
     * For validated users: fetch real balance.
     * For guests: reset to base mana (3).
     */
    public function actionSyncMana()
    {
        $apiBase = Yii::$app->params['tabnewsApiBase'] ?? 'https://www.tabnews.com.br/api/v1/';
        $client = new Client(['base_uri' => $apiBase, 'timeout' => 10]);
        $synced = 0;
        $failed = 0;

        $users = User::find()->all();
        foreach ($users as $user) {
            if ($user->is_validated && $user->tabnews_id) {
                try {
                    $res = $client->get('users/' . urlencode($user->tabnews_id));
                    $data = json_decode($res->getBody(), true);
                    $tabcoins = $data['tabcoins'] ?? 0;
                    $user->tabcoins_balance = $tabcoins;
                    $user->mana_weekly = max(3, $tabcoins);
                    $user->save(false, ['tabcoins_balance', 'mana_weekly']);
                    $synced++;
                } catch (\Exception $e) {
                    $failed++;
                    Yii::error("Mana sync failed for {$user->tabnews_id}: " . $e->getMessage());
                }
            } else {
                // Guest: reset to base mana
                $user->mana_weekly = 3;
                $user->save(false, ['mana_weekly']);
                $synced++;
            }
        }

        $msg = date('Y-m-d H:i:s') . " [MANA SYNC] Synced: {$synced}, Failed: {$failed}.";
        $this->log($msg);
        $this->stdout($msg . "\n");

        return ExitCode::OK;
    }

    /**
     * Append message to reaper log.
     */
    private function log(string $message): void
    {
        file_put_contents($this->logFile, $message . "\n", FILE_APPEND | LOCK_EX);
    }
}
