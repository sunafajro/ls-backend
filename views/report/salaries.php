<?php
  $this->title = 'Система учета :: '.Yii::t('app','Reports');
  $this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => '/report/index'];
  $this->params['breadcrumbs'][] = Yii::t('app','Salaries');
?>

<div id="app" data-nullyear="2011"></div>

<?php
    $this->registerJsFile('/js/reports/salaries/vendors-' . (Yii::$app->params['appMode'] === 'bitrix' ? 'bitrix.js' : 'standalone.js'),
    ['position' => yii\web\View::POS_END]);
    $this->registerJsFile('/js/reports/salaries/app-' . (Yii::$app->params['appMode'] === 'bitrix' ? 'bitrix.js' : 'standalone.js'),
    ['position' => yii\web\View::POS_END]);
?>