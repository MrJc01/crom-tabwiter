<?php
/** @var yii\web\View $this */
/** @var string $content */

use yii\helpers\Html;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>

    <!-- React Stack (Production) -->
    <script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
    <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        tab: {
                            bg: '#f8f9fa',
                            text: '#1f2937',
                            border: '#e5e7eb',
                            accent: '#6366f1',
                            link: '#2563eb',
                            muted: '#6b7280'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>

    <!-- Icons: Stable UMD Version -->
    <script src="https://unpkg.com/lucide@0.263.1/dist/umd/lucide.min.js"></script>
    <script src="https://unpkg.com/lucide-react@0.263.1/dist/umd/lucide-react.min.js"></script>

    <!-- Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=JetBrains+Mono:wght@400&display=swap"
        rel="stylesheet">

    <?php $this->head() ?>
</head>

<body class="bg-tab-bg text-tab-text font-sans">
    <?php $this->beginBody() ?>

    <div id="root"></div>

    <?= $content ?>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>