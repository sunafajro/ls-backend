<?php
/**
 * @var $this yii\web\View
 */
$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'References');
$this->params['breadcrumbs'][] = Yii::t('app', 'References');
?>
<div id="app" data-mode="<?= Yii::$app->params['appMode'] ?>"></div>
<?php
$this->registerJsFile('/js/references/vendors-' . Yii::$app->params['appMode'] . '.js', ['position' => yii\web\View::POS_END]);
$this->registerJsFile('/js/references/app-' . Yii::$app->params['appMode'] . '.js', ['position' => yii\web\View::POS_END]);
?>