<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    // Tailwind via CDN to keep things simple for prototype
    public $css = [
        'https://cdn.jsdelivr.net/npm/tailwindcss@3.4.2/dist/tailwind.min.css',
        'css/site.css',
    ];
    public $js = [
        'https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        // bootstrap is no longer a dependency; remove to keep payload small
    ];
}
