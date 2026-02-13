<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Post;
use app\models\PostTag;
use app\models\User;
use GuzzleHttp\Client;

class SiteController extends Controller
{
    /**
     * Displays the main feed (React App).
     * Now handles the logic previously in PostManager::actionIndex.
     */
    public function actionIndex()
    {
        // Auto-login logic (from AuthController/PostController)
        if (Yii::$app->user->isGuest) {
            $hash = Yii::$app->request->cookies->getValue('tw_hash');
            if ($hash) {
                $user = User::findIdentityByAccessToken($hash);
                if ($user) {
                    Yii::$app->user->login($user);
                }
            }
            if (Yii::$app->user->isGuest) {
                $user = User::createGuest();
                Yii::$app->user->login($user, 3600 * 24 * 7);
                Yii::$app->response->cookies->add(new \yii\web\Cookie([
                    'name' => 'tw_hash',
                    'value' => $user->auth_hash,
                    'expire' => time() + 3600 * 24 * 30,
                ]));
            }
        }

        $currentUser = Yii::$app->user->identity;
        if ($currentUser) {
            $currentUser->updateActivity();
        }

        // Fetch local posts
        $posts = Post::find()
            ->with('user')
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(50)
            ->all();

        // Fetch external/trending data
        $trendingData = PostTag::getTrending(5);
        $trending = array_map(function ($t) {
            return [
                'tag' => $t['tag'],
                'count' => (int) $t['cnt'], // React expects 'count'
            ];
        }, $trendingData);

        return $this->render('index', [
            'posts' => $posts,
            'trending' => $trending,
            'currentUser' => $currentUser,
            'external' => [], // Placeholder for now or fetch TabNews if needed
        ]);
    }

    /**
     * Error handler.
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
}
