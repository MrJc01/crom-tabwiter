<?php
/** @var yii\web\View $this */
/** @var app\models\Post[] $posts */

use yii\helpers\Html;
?>
<div class="space-y-4">
    <?php foreach ($posts as $post): ?>
        <div class="bg-white p-4 rounded shadow">
            <div class="text-sm text-gray-500 mb-2">Posted by <?= Html::encode($post->user->username ?? 'unknown') ?> at <?= date('Y-m-d H:i', $post->created_at) ?></div>
            <div class="whitespace-pre-wrap"><?= Html::encode($post->content) ?></div>
            <div class="mt-2 text-xs text-gray-600">Points: <?= $post->points ?></div>
        </div>
    <?php endforeach; ?>
</div>