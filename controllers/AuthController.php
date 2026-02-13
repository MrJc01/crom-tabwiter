<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\User;
use GuzzleHttp\Client;

class AuthController extends Controller
{
    /**
     * Bridge action allowing TabNews users to validate themselves by
     * publishing the local auth_hash in their profile description.
     * GET or POST parameter: username
     *
     * Returns JSON with keys:
     *  - validated: bool
     *  - auth_hash: string (when validated or new guest created)
     *  - error/message on failure
     */
    public function actionTabnewsValidate($username = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (empty($username)) {
            return ['error' => 'username required'];
        }

        // find or create user record
        $user = User::findByUsername($username);
        if (!$user) {
            $user = new User();
            $user->username = $username;
            // auth_hash will be generated automatically
            if (!$user->save()) {
                return ['error' => 'cannot create user', 'details' => $user->errors];
            }
        }

        // fetch TabNews profile data
        $client = new Client(['base_uri' => 'https://tabnews.com.br/api/']);
        try {
            $res = $client->get("users/" . urlencode($username));
            $data = json_decode($res->getBody(), true);
            $description = $data['description'] ?? '';
            $found = strpos($description, $user->auth_hash) !== false;
            if ($found) {
                $user->is_validated = true;
                $user->save(false, ['is_validated']);
                return ['validated' => true, 'auth_hash' => $user->auth_hash];
            }
            return ['validated' => false];
        } catch (\Exception $e) {
            return ['error' => 'fetch_failed', 'message' => $e->getMessage()];
        }
    }
}
