<?php

/** @var yii\web\View $this */

$this->title = 'TabWiter - Ecossistema Dev';
?>
<div class="text-center py-20">
    <h1 class="text-2xl font-bold mb-4 text-stone-900">Bem-vindo ao TabWiter</h1>
    <p class="text-stone-500 mb-6">Redirecionando para o feed...</p>
    <a href="<?= Yii::$app->urlManager->createUrl(['post/index']) ?>"
        class="inline-block px-6 py-3 bg-stone-900 text-white rounded-lg font-medium hover:bg-stone-800 transition-colors">Ir
        para o Feed</a>
</div>
<script>
    // Auto-redirect to feed
    window.location.href = '<?= Yii::$app->urlManager->createUrl(["post/index"]) ?>';
</script>