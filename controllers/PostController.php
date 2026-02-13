<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use app\models\Post;
use app\models\User;
use GuzzleHttp\Client;

class PostController extends Controller
{
    /**
     * show feed; optionally merge TabNews posts based on interests sent via GET
     */
    public function actionIndex()
    {
        $local = Post::find()->orderBy(['created_at' => SORT_DESC])->all();

        // hybrid feed: if interests provided, fetch popular posts from TabNews
        $external = [];
        $interests = Yii::$app->request->get('interests');
        if ($interests) {
            try {
                $client = new Client(['base_uri' => 'https://tabnews.com.br/api/']);
                // this is a rough example; TabNews may not offer this endpoint
                $res = $client->get('posts/popular', [
                    'query' => ['tags' => implode(',', explode(',', $interests))]
                ]);
                $external = json_decode($res->getBody(), true) ?: [];
            } catch (\Exception $e) {
                Yii::error('TabNews fetch failed: ' . $e->getMessage());
            }
        }

        return $this->render('index', ['posts' => $local, 'external' => $external]);
    }

    /**
     * create a new post via API (JSON). requires auth_hash.
     */
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $body = Yii::$app->request->getRawBody();
        $data = json_decode($body, true);
        if (!$data || empty($data['auth_hash']) || empty($data['content'])) {
            throw new BadRequestHttpException('auth_hash and content required');
        }

        $user = User::findOne(['auth_hash' => $data['auth_hash']]);
        if (!$user) {
            return ['error' => 'invalid auth_hash'];
        }

        $post = new Post();
        $post->user_id = $user->id;
        $post->content = $data['content'];
        $post->created_at = time();
        $post->points = 1;
        $post->is_tabnews_sync = false;
        if ($post->save()) {
            return ['status' => 'ok', 'id' => $post->id];
        }
        return ['error' => 'save_failed', 'details' => $post->errors];
    }
}
