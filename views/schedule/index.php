<?php
/**
 * @var $this yii\web\View
 */
$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Schedule');
$this->params['breadcrumbs'][] = Yii::t('app','Schedule');
?>
<div id="app"></div>
<?php
$this->registerJsFile('/js/schedule/vendors-' . (Yii::$app->params['appMode'] === 'bitrix' ? 'bitrix.js' : 'standalone.js'),
    ['position' => yii\web\View::POS_END]);
$this->registerJsFile('/js/schedule/app-' . (Yii::$app->params['appMode'] === 'bitrix' ? 'bitrix.js' : 'standalone.js'),
    ['position' => yii\web\View::POS_END]);
?>