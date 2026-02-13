<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $auth_key
 * @property int $tabcoins_balance
 * @property int $mana_weekly
 * @property bool $is_validated
 * @property int $last_active_at
 * @property int $created_at
 * @property int $updated_at
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class, // handles created_at and updated_at
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'email', 'password_hash'], 'required'],
            [['username', 'email', 'password_hash', 'auth_key'], 'string', 'max' => 255],
            [['username', 'email'], 'unique'],
            [['tabcoins_balance', 'mana_weekly', 'last_active_at', 'created_at', 'updated_at'], 'integer'],
            [['is_validated'], 'boolean'],
            ['mana_weekly', 'default', 'value' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // Standalone usually uses session, but IF we need API token later:
        // For now, return null unless we implement concrete access token logic
        // Or if we use auth_key as simple token for cookie login (Yii2 standard)
        return null;
    }

    /**
     * Finds user by username (or email if you prefer, but standard is username)
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    // --- TabWiter Business Logic ---

    /**
     * Update last_active_at timestamp.
     */
    public function updateActivity()
    {
        $this->last_active_at = time();
        return $this->save(false, ['last_active_at']);
    }

    /**
     * Check if user has mana available to vote.
     */
    public function hasMana(): bool
    {
        return $this->mana_weekly > 0;
    }

    /**
     * Spend one unit of mana.
     */
    public function spendMana(): bool
    {
        if (!$this->hasMana()) {
            return false;
        }
        $this->mana_weekly--;
        return $this->save(false, ['mana_weekly']);
    }

    /**
     * Relation: User has many Posts
     */
    public function getPosts()
    {
        return $this->hasMany(Post::class, ['user_id' => 'id']);
    }
}
