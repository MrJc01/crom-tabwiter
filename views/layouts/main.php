<?php

/** @var yii\web\View $this */
/** @var string $content */

use yii\helpers\Html;
use app\assets\AppAsset;
use yii\helpers\Url;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-full bg-slate-50">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>

    <!-- React & ReactDOM -->
    <script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>
    
    <!-- Babel for JSX -->
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- FIX: Lucide React Compatibility -->
    <script>
        window.react = window.React;
        window.reactDom = window.ReactDOM;
    </script>
    <script src="https://unpkg.com/lucide-react@0.263.1/dist/umd/lucide-react.min.js"></script>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        tab: {
                            bg: '#FAFAFA',
                            card: '#FFFFFF',
                            border: '#E5E5E5',
                            text: '#171717',
                            muted: '#737373',
                            accent: '#238636', 
                            accentLight: '#dafbe1',
                            link: '#0969DA',   
                            danger: '#CF222E'
                        }
                    },
                    animation: {
                        'enter': 'enter 0.3s ease-out forwards',
                    },
                    keyframes: {
                        enter: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #FAFAFA;
            color: #171717;
            -webkit-font-smoothing: antialiased;
        }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #E5E5E5; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #d4d4d4; }
    </style>

    <?php $this->head() ?>
</head>

<body class="h-full text-slate-800 antialiased">
    <?php $this->beginBody() ?>

    <?php if (Yii::$app->user->isGuest && Yii::$app->controller->route === 'site/index'): ?>
        <!-- Full Screen Landing Hero for Guests -->
        <?= $this->render('//site/_landing') ?>
    <?php endif; ?>

    <!-- React Root -->
    <?= $content ?>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>