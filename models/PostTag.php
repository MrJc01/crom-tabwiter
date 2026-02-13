<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * PostTag model – hashtag relation for posts.
 *
 * @property int $id
 * @property int $post_id
 * @property string $tag
 */
class PostTag extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%post_tag}}';
    }

    public function rules()
    {
        return [
            [['post_id', 'tag'], 'required'],
            [['post_id'], 'integer'],
            [['tag'], 'string', 'max' => 100],
        ];
    }

    public function getPost()
    {
        return $this->hasOne(Post::class, ['id' => 'post_id']);
    }

    /**
     * Get trending tags (most used in the last 24h).
     *
     * @param int $limit
     * @return array [['tag' => string, 'count' => int], ...]
     */
    public static function getTrending(int $limit = 5): array
    {
        $since = time() - 86400;
        return static::find()
            ->select(['tag', 'COUNT(*) as cnt'])
            ->innerJoin('{{%post}}', '{{%post}}.id = {{%post_tag}}.post_id')
            ->where(['>=', '{{%post}}.created_at', $since])
            ->groupBy('tag')
            ->orderBy(['cnt' => SORT_DESC])
            ->limit($limit)
            ->asArray()
            ->all();
    }
}
