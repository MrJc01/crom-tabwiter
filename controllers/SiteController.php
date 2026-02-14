<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\SignupForm;
use app\models\User;
use app\models\Post;
use yii\helpers\Url;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'index', 'create-post', 'upvote'],
                'rules' => [
                    [
                        'actions' => ['logout', 'create-post', 'upvote'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index'], // Index accessible by both (conditional render)
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                    'create-post' => ['post'],
                    'upvote' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $posts = Post::find()
            ->with(['user', 'tags']) // optimize query
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(50)
            ->all();

        // Prepare Data for React
        $postsData = [];
        foreach ($posts as $post) {
            $postsData[] = [
                'id' => $post->id,
                'type' => 'tabet', // Default type
                'title' => '', // Only for articles
                'user' => $post->user->username, // Name
                'handle' => $post->user->username, // Handle
                'time' => Yii::$app->formatter->asRelativeTime($post->created_at),
                'content' => $post->content,
                'stats' => [ // Mock stats for now, except score
                    'comments' => 0,
                    'reposts' => 0,
                    'score' => $post->points ?? 0
                ],
                'verified' => false,
                // Mock comments
                'commentsData' => []
            ];
        }

        $userData = null;
        if (!Yii::$app->user->isGuest) {
            $identity = Yii::$app->user->identity;
            $userData = [
                'name' => $identity->username,
                'handle' => $identity->username,
                'bio' => "Mana: " . ($identity->mana_weekly ?? 0),
                'location' => 'TabWiter',
                'website' => '',
                'joined' => 'Hoje',
                'tabcoins' => 0,
                'verified' => true,
                'followers' => "0",
                'following' => "0"
            ];
        }

        $initialData = [
            'posts' => $postsData,
            'user' => $userData,
            'isGuest' => Yii::$app->user->isGuest,
            'urls' => [
                'login' => Url::to(['site/login']),
                'signup' => Url::to(['site/signup']),
                'logout' => Url::to(['site/logout']),
                'createPost' => Url::to(['site/create-post']),
                'upvote' => Url::to(['site/upvote']),
                'universes' => Url::to(['site/universes']),
            ]
        ];

        return $this->render('index', [
            'initialData' => $initialData,
        ]);
    }

    /**
     * Displays universes status page.
     *
     * @return string
     */
    public function actionUniverses()
    {
        return $this->renderPartial('_universes');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Signup action.
     *
     * @return Response|string
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Create Post Action
     */
    public function actionCreatePost()
    {
        $model = new Post();
        $model->user_id = Yii::$app->user->id;
        $model->created_at = time();

        if ($model->load(Yii::$app->request->post(), '') && $model->save()) {
            // Spend Mana?
            // "actionUpvote: Verifica saldo de Mana. Subtrai 1 de Mana"
            // Prompt doesn't explicitly say posting costs mana, strictly says "upvote costs mana".
            // But usually posting might cost mana. I'll stick to strictly prompt "Votos, Login, Cadastro...  actionCreatePost: Salva e retorna JSON".

            // Check if Request is Ajax/JSON
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => true,
                    'post' => [
                        'id' => $model->id,
                        'content' => $model->content, // Format content?
                        'username' => $model->user->username,
                        'created_at' => Yii::$app->formatter->asRelativeTime($model->created_at),
                        'points' => $model->points ?? 0
                    ]
                ];
            }
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => false, 'errors' => $model->errors];
        }

        return $this->redirect(['index']);
    }

    /**
     * Upvote Action
     */
    public function actionUpvote($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $user = User::findOne(Yii::$app->user->id);
        $post = Post::findOne($id);

        if (!$user || !$post) {
            return ['success' => false, 'message' => 'User or Post not found.'];
        }

        if (!$user->hasMana()) {
            return ['success' => false, 'message' => 'Not enough Mana.'];
        }

        // Transaction to ensure atomicity
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Deduct Mana
            $user->spendMana();

            // Add point to post
            $post->points = ($post->points ?? 0) + 1;
            $post->save(false, ['points']);

            // Should we record the vote to prevent double voting?
            // "actionUpvote: Verifica saldo de Mana. Subtrai 1 de Mana, adiciona 1 ao Post."
            // Doesn't strictly say "USER CAN ONLY VOTE ONCE". 
            // Usually gamification allows spending mana freely.
            // But verify "hasVoteFrom" in Post model.
            // If I want to allow multiple votes, I ignore hasVoteFrom.
            // Prompt says: "O projeto é agora uma rede social standalone... mecânica de gamificação (Mana/Decaimento)."
            // Stick to simple: Spend mana -> Upvote.

            $transaction->commit();

            return [
                'success' => true,
                'points' => $post->points,
                'newMana' => $user->mana_weekly
            ];

        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success' => false, 'message' => 'Error processing vote.'];
        }
    }
}
