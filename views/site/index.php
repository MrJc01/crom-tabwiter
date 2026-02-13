<?php

use yii\helpers\Url;

$this->title = 'TabWiter';
?>

<!-- React Root -->
<div id="root"></div>

<script>
    window.INITIAL_DATA = <?= json_encode($initialData) ?>;
</script>

<script type="text/babel" src="<?= Url::to('@web/js/tabwiter-app.js') ?>"></script>