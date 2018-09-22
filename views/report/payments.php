<?php
  $this->title = 'Система учета :: '.Yii::t('app','Reports');
  $this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => 'report/index'];
  $this->params['breadcrumbs'][] = Yii::t('app','Payments');
?>

<div id="app" data-nullyear="2011"></div>

<?php
    $this->registerJsFile('/js/reports/vendors.js',
    ['position' => yii\web\View::POS_END]);
    $this->registerJsFile('/js/reports/app.js',
    ['position' => yii\web\View::POS_END]);
?>