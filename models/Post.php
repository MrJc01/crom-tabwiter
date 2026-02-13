<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;

/**
 * Post model for TabWiter feed (ephemeral tabets).
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
            [['content'], 'string', 'max' => Yii::$app->params['postMaxLength'] ?? 500],
            [['is_tabnews_sync'], 'boolean'],
        ];
    }

    /**
     * Relation to author.
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Relation to tags.
     */
    public function getTags()
    {
        return $this->hasMany(PostTag::class, ['post_id' => 'id']);
    }

    /**
     * Extract hashtags from content and save to post_tag table.
     */
    public function extractAndSaveTags()
    {
        // Delete existing tags for this post
        PostTag::deleteAll(['post_id' => $this->id]);

        // Extract unique hashtags
        if (preg_match_all('/#(\w+)/u', $this->content, $matches)) {
            $uniqueTags = array_unique(array_map('strtolower', $matches[1]));
            foreach ($uniqueTags as $tag) {
                $pt = new PostTag();
                $pt->post_id = $this->id;
                $pt->tag = $tag;
                $pt->save();
            }
        }
    }

    /**
     * Calculate how many days until this post dies.
     */
    public function getLifeExpectancy(): int
    {
        $threshold = Yii::$app->params['deathThreshold'] ?? -10;
        $decay = Yii::$app->params['decayPerDay'] ?? 1;
        // Distance from current points to death
        $distance = $this->points - $threshold;
        return max(0, (int) ceil($distance / $decay));
    }

    /**
     * Get life status color class.
     */
    public function getLifeColor(): string
    {
        if ($this->points > 5)
            return 'green';
        if ($this->points >= 0)
            return 'gray';
        return 'red';
    }

    /**
     * Apply daily decay: subtract points, delete if below threshold.
     *
     * @return bool whether post still exists after decay
     */
    public function applyDecay()
    {
        $this->points -= (Yii::$app->params['decayPerDay'] ?? 1);
        $threshold = Yii::$app->params['deathThreshold'] ?? -10;

        if ($this->points <= $threshold) {
            // Clean up tags before deleting
            PostTag::deleteAll(['post_id' => $this->id]);
            return $this->delete() !== false;
        }
        return $this->save(false, ['points']);
    }

    /**
     * Check if a user has already voted on this post.
     */
    public function hasVoteFrom(int $userId): bool
    {
        return Vote::find()
            ->where(['user_id' => $userId, 'post_id' => $this->id])
            ->exists();
    }

    /**
     * Apply basic markdown-like formatting.
     * Converts **bold**, *italic*, `code`, and line breaks.
     */
    public static function formatContent(string $raw): string
    {
        // Escape HTML
        $text = htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');

        // Bold **text**
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
        // Italic *text*
        $text = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $text);
        // Code `text`
        $text = preg_replace('/`(.+?)`/', '<code class="bg-stone-100 px-1 rounded text-sm">$1</code>', $text);
        // Links [text](url)
        $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" target="_blank" class="text-blue-600 hover:underline">$1</a>', $text);
        // Line breaks
        $text = nl2br($text);

        return $text;
    }
}
