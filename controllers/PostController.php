<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\Post;
use app\models\PostTag;
use app\models\User;
use app\models\Vote;
use GuzzleHttp\Client;

/**
 * PostController – API-only controller for Posts.
 * (Removed actionIndex, as it's now in SiteController)
 */
class PostController extends Controller
{
    /**
     * Disable CSRF for JSON API actions.
     */
    public function beforeAction($action)
    {
        if (in_array($action->id, ['create', 'upvote', 'get-hybrid-feed'])) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * Create a new post (tabet). JSON API.
     */
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->user->isGuest) {
            return ['error' => 'not_logged_in'];
        }

        $data = Yii::$app->request->getBodyParams();
        $content = trim($data['content'] ?? '');
        $maxLen = Yii::$app->params['postMaxLength'] ?? 500;

        if (empty($content)) {
            return ['error' => 'Conteúdo não pode ser vazio.'];
        }
        if (mb_strlen($content) > $maxLen) {
            return ['error' => "Limite de {$maxLen} caracteres excedido."];
        }

        $user = Yii::$app->user->identity;
        $user->updateActivity();

        $post = new Post();
        $post->user_id = $user->id;
        $post->content = strip_tags($content); // sanitize
        $post->created_at = time();
        $post->points = 1;
        $post->is_tabnews_sync = false;

        if ($post->save()) {
            $post->extractAndSaveTags();
            return [
                'status' => 'ok',
                'id' => $post->id,
                'post' => [
                    'id' => $post->id,
                    'username' => $user->username,
                    'content' => $post->content,
                    'content_html' => Post::formatContent($post->content),
                    'points' => $post->points,
                    'life' => $post->getLifeExpectancy(),
                    'lifeColor' => $post->getLifeColor(),
                    'created_at' => $post->created_at,
                    'timeAgo' => 'agora', // Simplified for immediate return
                ],
            ];
        }
        return ['error' => 'save_failed', 'details' => $post->errors];
    }

    /**
     * Upvote a post. Requires mana. Blocks self-vote.
     */
    public function actionUpvote($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->user->isGuest) {
            return ['error' => 'not_logged_in'];
        }

        $user = Yii::$app->user->identity;
        $post = Post::findOne($id);

        if (!$post) {
            return ['error' => 'Post não encontrado.'];
        }

        // Block self-vote
        if ($post->user_id === $user->id) {
            return ['error' => 'Você não pode votar no próprio post.'];
        }

        // Check mana
        if (!$user->hasMana()) {
            return ['error' => 'Mana insuficiente. Aguarde o reset semanal ou valide sua conta TabNews.'];
        }

        // Check double vote
        if ($post->hasVoteFrom($user->id)) {
            return ['error' => 'Você já votou neste post.'];
        }

        // Execute vote
        $vote = new Vote();
        $vote->user_id = $user->id;
        $vote->post_id = $post->id;
        $vote->created_at = time();

        if ($vote->save()) {
            $user->spendMana();
            $post->points++;
            $post->save(false, ['points']);
            $user->updateActivity();

            return [
                'status' => 'ok',
                'points' => $post->points,
                'mana' => $user->mana_weekly,
                'life' => $post->getLifeExpectancy(),
                'lifeColor' => $post->getLifeColor(),
            ];
        }

        return ['error' => 'Erro ao registrar voto.'];
    }

    /**
     * Hybrid feed endpoint: returns JSON of posts sorted by interests.
     */
    public function actionGetHybridFeed()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $interests = Yii::$app->request->get('interests', '');
        $interestTags = array_filter(array_map('trim', explode(',', $interests)));

        $query = Post::find()->with('user')->orderBy(['created_at' => SORT_DESC])->limit(50);

        // If interests provided, prioritize tagged posts
        if (!empty($interestTags)) {
            $taggedIds = PostTag::find()
                ->select('post_id')
                ->where(['tag' => $interestTags])
                ->column();

            if (!empty($taggedIds)) {
                // Sort: tagged posts first, then the rest
                $query->orderBy([
                    new \yii\db\Expression("CASE WHEN id IN (" . implode(',', array_map('intval', $taggedIds)) . ") THEN 0 ELSE 1 END"),
                    'created_at' => SORT_DESC,
                ]);
            }
        }

        $posts = $query->all();
        $result = [];
        foreach ($posts as $post) {
            $result[] = [
                'id' => $post->id,
                'username' => $post->user ? $post->user->username : 'anon',
                'content' => $post->content,
                'content_html' => Post::formatContent($post->content),
                'points' => $post->points,
                'life' => $post->getLifeExpectancy(),
                'lifeColor' => $post->getLifeColor(),
                'created_at' => $post->created_at,
                // 'timeAgo' handled by client or Post helper
            ];
        }

        return $result;
    }
}
