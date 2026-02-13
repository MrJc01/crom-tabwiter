<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\models\User;

/**
 * Post model for TabWiter feed.
 *
 * @property int $id
 * @property int $user_id
 * @property string $content
 * @property int $points
 * @property bool $is_tabnews_sync
 * @property int $created_at
 */
class Post extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%post}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'content'], 'required'],
            [['user_id', 'points', 'created_at'], 'integer'],
            [['content'], 'string'],
            [['is_tabnews_sync'], 'boolean'],
        ];
    }

    /**
     * relation to author
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * apply decay on points and delete record when very negative
     *
     * @return bool whether post remains after decay
     */
    public function applyDecay()
    {
        $this->points--;
        if ($this->points <= -10) {
            return $this->delete() !== false;
        }
        return $this->save(false, ['points']);
    }
}
