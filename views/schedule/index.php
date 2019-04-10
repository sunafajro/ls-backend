<?php
/**
 * @var $this yii\web\View
 */
$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Schedule');
$this->params['breadcrumbs'][] = Yii::t('app', 'Schedule');
?>
<div id="app" data-mode="<?=Yii::$app->params['appMode']?>"></div>
<?php
$this->registerJsFile('/js/schedule/vendors.js', ['position' => yii\web\View::POS_END]);
$this->registerJsFile('/js/schedule/app.js', ['position' => yii\web\View::POS_END]);
?>