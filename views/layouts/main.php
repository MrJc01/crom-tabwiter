<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\helpers\Html;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <!-- fallback CSS when external resources are unavailable -->
    <style>
        body { font-family: system-ui, sans-serif; background:#f3f4f6; color:#111; margin:0; }
        header { background:#fff; border-bottom:1px solid #ddd; position:fixed; width:100%; z-index:20; }
        main { padding-top:4rem; max-width:42rem; margin:0 auto; }
        aside { padding:1rem; background:#fff; }
        footer { text-align:center; padding:1rem; font-size:.875rem; color:#555; }
        nav a { margin-right:1rem; color:#1d4ed8; text-decoration:none; }
        nav a:hover { text-decoration:underline; }
        .space-y-4 > * + * { margin-top:1rem; }
        .bg-white { background:#fff; }
        .p-4 { padding:1rem; }
        .rounded { border-radius:0.25rem; }
        .shadow { box-shadow:0 1px 3px rgba(0,0,0,0.1); }
        .text-sm { font-size:0.875rem; }
        .text-gray-500 { color:#6b7280; }
        .text-gray-600 { color:#4b5563; }
        .text-gray-900 { color:#111827; }
        .mt-2 { margin-top:0.5rem; }
        .mb-2 { margin-bottom:0.5rem; }
        .whitespace-pre-wrap { white-space:pre-wrap; }
    </style>
</head>
<body class="bg-gray-100 text-gray-900">
<?php $this->beginBody() ?>

<header class="bg-white border-b fixed w-full z-20">
    <div class="max-w-4xl mx-auto flex items-center justify-between py-3 px-4">
        <a href="<?= Yii::$app->homeUrl ?>" class="font-bold text-xl"><?= Html::encode(Yii::$app->name) ?></a>
        <nav class="space-x-4">
            <a href="<?= Yii::$app->homeUrl ?>" class="hover:underline">Feed</a>
            <?php if (Yii::$app->user->isGuest): ?>
                <a href="<?= Yii::$app->urlManager->createUrl(['/site/login']) ?>" class="hover:underline">Login</a>
            <?php else: ?>
                <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'inline']) ?>
                    <?= Html::submitButton('Logout (' . Yii::$app->user->identity->username . ')', ['class' => 'text-red-500 hover:underline']) ?>
                <?= Html::endForm() ?>
            <?php endif ?>
        </nav>
    </div>
</header>

<div class="pt-16 flex justify-center">
    <aside class="hidden md:block w-64 px-4">
        <!-- left nav: home, profile, settings... -->
        <nav class="space-y-2">
            <a href="#" class="block px-2 py-1 hover:bg-gray-200 rounded">Home</a>
            <a href="#" class="block px-2 py-1 hover:bg-gray-200 rounded">Profile</a>
            <a href="#" class="block px-2 py-1 hover:bg-gray-200 rounded">Settings</a>
        </nav>
    </aside>

    <main class="flex-1 max-w-2xl px-4">
        <?= Alert::widget() ?>
        <?= $content ?>
    </main>

    <aside class="hidden lg:block w-64 px-4">
        <!-- right sidebar: mana status / interests -->
        <div class="bg-white p-3 rounded shadow">
            <h2 class="font-semibold mb-2">Mana</h2>
            <p>Weekly mana: <span x-text="0"></span></p>
            <!-- placeholder for reactive widget -->
        </div>
    </aside>
</div>

<footer class="text-center py-4 mt-8 text-sm text-gray-600">
    &copy; <?= date('Y') ?> <?= Html::encode(Yii::$app->name) ?> - powered by Yii2
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
