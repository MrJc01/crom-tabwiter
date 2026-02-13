<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 * CDNs (React, Tailwind, Lucide) are loaded directly in the layout.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $js = [];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
