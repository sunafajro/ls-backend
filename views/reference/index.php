<?php
    $this->title = 'Система учета :: ' . Yii::t('app','References');
?>

<div id="app"></div>

<?php
    $this->registerJsFile('/js/references/vendors-' . (Yii::$app->params['appMode'] === 'bitrix' ? 'bitrix.js' : 'standalone.js'),
    ['position' => yii\web\View::POS_END]);
    $this->registerJsFile('/js/references/app-' . (Yii::$app->params['appMode'] === 'bitrix' ? 'bitrix.js' : 'standalone.js'),
    ['position' => yii\web\View::POS_END]);
?>