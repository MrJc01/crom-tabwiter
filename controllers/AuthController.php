<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\User;
use GuzzleHttp\Client;

/**
 * AuthController – Guest auto-login, TabNews validation, logout.
 */
class AuthController extends Controller
{
    /**
     * Disable CSRF for API-style JSON endpoints.
     */
    public $enableCsrfValidation = false;

    /**
     * Auto-login: create a guest user if not already logged in.
     * The auth_hash is stored in a cookie and used as session key.
     */
    public function actionAutoLogin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // If already logged in, just return the current user
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            $user->updateActivity();
            return [
                'status' => 'existing',
                'auth_hash' => $user->auth_hash,
                'username' => $user->username,
                'mana' => $user->mana_weekly,
                'validated' => (bool) $user->is_validated,
            ];
        }

        // Check if there's an auth_hash in the cookie
        $hashFromCookie = Yii::$app->request->cookies->getValue('tw_hash');
        if ($hashFromCookie) {
            $user = User::findIdentityByAccessToken($hashFromCookie);
            if ($user) {
                Yii::$app->user->login($user, 3600 * 24 * 7);
                $user->updateActivity();
                return [
                    'status' => 'restored',
                    'auth_hash' => $user->auth_hash,
                    'username' => $user->username,
                    'mana' => $user->mana_weekly,
                    'validated' => (bool) $user->is_validated,
                ];
            }
        }

        // Create a brand new guest user
        $user = User::createGuest();
        Yii::$app->user->login($user, 3600 * 24 * 7);

        // Set cookie with auth_hash
        Yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name' => 'tw_hash',
            'value' => $user->auth_hash,
            'expire' => time() + 3600 * 24 * 30,
        ]));

        return [
            'status' => 'created',
            'auth_hash' => $user->auth_hash,
            'username' => $user->username,
            'mana' => $user->mana_weekly,
            'validated' => false,
        ];
    }

    /**
     * Validate TabNews identity via bio description.
     * Also captures tabcoins_balance from the API.
     */
    public function actionTabnewsValidate($username = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (empty($username)) {
            return ['error' => 'username required'];
        }

        // Must be logged in
        if (Yii::$app->user->isGuest) {
            return ['error' => 'not_logged_in'];
        }

        $user = Yii::$app->user->identity;

        // Fetch TabNews profile
        $apiBase = Yii::$app->params['tabnewsApiBase'] ?? 'https://www.tabnews.com.br/api/v1/';
        $client = new Client(['base_uri' => $apiBase, 'timeout' => 10]);

        try {
            $res = $client->get('users/' . urlencode($username));
            $data = json_decode($res->getBody(), true);
            $description = $data['description'] ?? '';
            $tabcoins = $data['tabcoins'] ?? 0;

            // Check if auth_hash is in the bio
            $found = strpos($description, $user->auth_hash) !== false;
            if ($found) {
                $user->is_validated = true;
                $user->tabnews_id = $username;
                $user->username = $username;
                $user->tabcoins_balance = $tabcoins;
                $user->mana_weekly = max($user->mana_weekly, $tabcoins);
                $user->save(false);
                return [
                    'validated' => true,
                    'auth_hash' => $user->auth_hash,
                    'username' => $username,
                    'tabcoins' => $tabcoins,
                    'mana' => $user->mana_weekly,
                ];
            }

            return [
                'validated' => false,
                'hint' => "Adicione '{$user->auth_hash}' na sua bio do TabNews e tente novamente.",
            ];
        } catch (\Exception $e) {
            return ['error' => 'fetch_failed', 'message' => $e->getMessage()];
        }
    }

    /**
     * Logout: clear session/cookie, keep the account in DB.
     */
    public function actionLogout()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        Yii::$app->user->logout();
        Yii::$app->response->cookies->remove('tw_hash');

        return ['status' => 'logged_out'];
    }
}
