<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Post;
use app\models\User;
use Yii;

/**
 * Reaper controller: scheduled maintenance tasks.
 *
 * Usage:
 *   php yii reaper/decay
 *   php yii reaper/cleanup-users
 *   php yii reaper/reset-mana
 */
class ReaperController extends Controller
{
    /**
     * Apply decay to local posts and optionally clean guests.
     * This command is intended to run daily via cron.
     *
     * @return int
     */
    public function actionDecay()
    {
        // apply decay to unsynced posts
        $posts = Post::find()->where(['is_tabnews_sync' => false])->all();
        foreach ($posts as $post) {
            /* @var $post Post */
            $post->applyDecay();
        }

        $this->stdout("Decay applied to " . count($posts) . " posts.\n");
        return ExitCode::OK;
    }

    /**
     * Remove guest users who have been inactive for more than 24h.
     * Can be run independently or invoked from actionDecay.
     *
     * @return int
     */
    public function actionCleanupUsers()
    {
        $threshold = time() - 86400;
        $count = User::deleteAll([
            'and',
            ['is_validated' => false],
            ['<', 'last_active_at', $threshold],
        ]);
        $this->stdout("Deleted $count inactive guest users.\n");
        return ExitCode::OK;
    }

    /**
     * Weekly synchronization of mana_weekly with actual tabcoins balance.
     * In a real implementation this would call TabNews API for each user;
     * here we simply copy the stored tabcoins_balance field.
     *
     * @return int
     */
    public function actionResetMana()
    {
        $users = User::find()->all();
        foreach ($users as $user) {
            /* @var $user User */
            $user->mana_weekly = $user->tabcoins_balance;
            $user->save(false, ['mana_weekly']);
        }
        $this->stdout("Reset mana for " . count($users) . " users.\n");
        return ExitCode::OK;
    }
}
