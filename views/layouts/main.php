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
    <script src="/js/libs/react.production.min.js"></script>
    <script src="/js/libs/react-dom.production.min.js"></script>

    <!-- Babel for JSX -->
    <script src="/js/libs/babel.min.js"></script>

    <!-- Tailwind CSS -->
    <script src="/js/libs/tailwindcss.js"></script>

    <!-- FIX: Lucide React Compatibility -->
    <script>
        window.react = window.React;
        window.reactDom = window.ReactDOM;
    </script>
    <script src="/js/libs/lucide-react.min.js"></script>

    <!-- Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

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

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #E5E5E5;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #d4d4d4;
        }

        /* Static Loader Styles */
        #global-loader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background-color: #FAFAFA;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.5s ease-out;
        }

        .loader-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            opacity: 0;
            transform: scale(0.5);
            transition: all 0.7s ease-out;
        }

        .loader-content.active {
            opacity: 1;
            transform: scale(1);
        }

        .loader-repo-name {
            font-family: 'Inter', sans-serif;
            font-size: 2.25rem;
            font-weight: 700;
            color: #171717;
            letter-spacing: -0.05em;
            opacity: 0;
            transform: translateY(1rem);
            transition: all 0.7s ease-out 0.2s;
        }

        .loader-repo-name.active {
            opacity: 1;
            transform: translateY(0);
        }

        .loader-bar-container {
            margin-top: 1.5rem;
            height: 4px;
            background-color: rgba(229, 229, 229, 0.5);
            border-radius: 9999px;
            overflow: hidden;
            width: 8rem;
            opacity: 0;
            transition: opacity 0.5s ease-out 0.5s;
        }

        .loader-bar-container.active {
            opacity: 1;
        }

        .loader-bar {
            height: 100%;
            background-color: #171717;
            width: 100%;
            transform-origin: left;
            animation: loading-bar 1.5s infinite ease-in-out;
        }

        @keyframes loading-bar {
            0% {
                transform: translateX(-100%);
            }

            50% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(100%);
            }
        }
    </style>

    <?php $this->head() ?>
</head>

<body class="h-full text-slate-800 antialiased">
    <!-- STATIC LOADING SCREEN -->
    <div id="global-loader">
        <div class="loader-content" id="loader-content">
            <!-- Icon: Terminal from Lucide -->
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="text-tab-text mb-6">
                <polyline points="4 17 10 11 4 5"></polyline>
                <line x1="12" y1="19" x2="20" y2="19"></line>
            </svg>
            <h1 class="loader-repo-name" id="loader-repo-name">TabWiter</h1>
            <div class="loader-bar-container" id="loader-bar">
                <div class="loader-bar"></div>
            </div>
        </div>
    </div>

    <script>
            (function () {
                // Check if user came from internal navigation (optional optimization)
                // For now, always play logic but maybe faster if session?

                // Animation Phases
                setTimeout(() => {
                    document.getElementById('loader-content').classList.add('active');
                    document.getElementById('loader-repo-name').classList.add('active');
                    document.getElementById('loader-bar').classList.add('active');
                }, 100);

                // Fade out and remove
                const MIN_TIME = 2000; // Minimum time to show logo
                const start = Date.now();

                window.removeLoader = function () {
                    const now = Date.now();
                    const elapsed = now - start;
                    const remaining = Math.max(0, MIN_TIME - elapsed);

                    setTimeout(() => {
                        const loader = document.getElementById('global-loader');
                        if (loader) {
                            loader.style.opacity = '0';
                            loader.style.pointerEvents = 'none';
                            setTimeout(() => {
                                loader.remove();
                            }, 500);
                        }
                    }, remaining);
                };

                // Fallback removal if React fails or takes too long (5s)
                setTimeout(window.removeLoader, 5000);
            })();
    </script>

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