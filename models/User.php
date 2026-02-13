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
                'value' => function () {
                    return time(); },
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
                $this->auth_hash = Yii::$app->security->generateRandomString();
            }
            return true;
        }
        return false;
    }

    // --- Identity Interface ---

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
     * Create a guest user with random name and hash.
     */
    public static function createGuest(): self
    {
        $user = new self();
        $user->username = 'guest_' . substr(Yii::$app->security->generateRandomString(8), 0, 8);
        $user->auth_hash = Yii::$app->security->generateRandomString();
        $user->tabcoins_balance = 0;
        $user->mana_weekly = 3; // guests start with 3 mana
        $user->is_validated = false;
        $user->last_active_at = time();
        $user->save();
        return $user;
    }

    /**
     * Get posts by this user.
     */
    public function getPosts()
    {
        return $this->hasMany(Post::class, ['user_id' => 'id']);
    }
}
