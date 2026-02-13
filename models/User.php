<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;
use Yii;

/**
 * User model backed by {{%user}} table.
 *
 * @property int $id
 * @property string $username
 * @property string $auth_hash
 * @property string|null $tabnews_id
 * @property int $tabcoins_balance
 * @property int $mana_weekly
 * @property bool $is_validated
 * @property int|null $last_active_at
 * @property int $created_at
 */
class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName()
    {
        return '{{%user}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => function() { return time(); },
            ],
        ];
    }

    public function rules()
    {
        return [
            [['username', 'auth_hash'], 'required'],
            [['username', 'tabnews_id'], 'string', 'max' => 255],
            [['tabcoins_balance', 'mana_weekly', 'last_active_at', 'created_at'], 'integer'],
            [['is_validated'], 'boolean'],
            ['username', 'unique'],
            ['auth_hash', 'string', 'max' => 255],
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && empty($this->auth_hash)) {
                // generate a random hash for guest access
                $this->auth_hash = Yii::$app->security->generateRandomString();
            }
            return true;
        }
        return false;
    }

    // IdentityInterface implementation
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_hash' => $token]);
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_hash;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_hash === $authKey;
    }
}
