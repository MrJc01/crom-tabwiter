<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Vote model – tracks which user voted on which post.
 *
 * @property int $id
 * @property int $user_id
 * @property int $post_id
 * @property int $created_at
 */
class Vote extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%vote}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'post_id'], 'required'],
            [['user_id', 'post_id', 'created_at'], 'integer'],
            [['user_id'], 'unique', 'targetAttribute' => ['user_id', 'post_id'], 'message' => 'Você já votou neste post.'],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getPost()
    {
        return $this->hasOne(Post::class, ['id' => 'post_id']);
    }
}
