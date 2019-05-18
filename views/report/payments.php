<?php

/**
 * @var yii\web\View $this
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Reports');
?>

<div id="app" data-mode="<?= Yii::$app->params['appMode'] ?>" data-nullyear="2011"></div>

<?php
    $this->registerJsFile('/js/reports/payments/vendors.js',
    ['position' => yii\web\View::POS_END]);
    $this->registerJsFile('/js/reports/payments/app.js',
    ['position' => yii\web\View::POS_END]);
?>