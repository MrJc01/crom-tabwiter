<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required', 'message' => 'Por favor, escolha um nome de usuário.'],
            ['username', 'unique', 'targetClass' => '\app\models\User', 'message' => 'Este nome de usuário já está em uso.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required', 'message' => 'O email é obrigatório.'],
            ['email', 'email', 'message' => 'Email inválido.'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'Este email já está registrado.'],

            ['password', 'required', 'message' => 'A senha é obrigatória.'],
            ['password', 'string', 'min' => 6, 'message' => 'A senha deve ter pelo menos 6 caracteres.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Usuário',
            'email' => 'E-mail',
            'password' => 'Senha',
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->mana_weekly = 100; // Default mana
        $user->created_at = time();
        $user->updated_at = time();
        $user->is_validated = true; // Auto validate for now, or false if email confirm needed. Prompt implies standalone social network, simpler "Login via Email/Senha"

        return $user->save();
    }
}
