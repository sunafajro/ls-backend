<?php
  $this->title = 'Система учета :: '.Yii::t('app','Clients');
  $this->params['breadcrumbs'][] = ['label' => Yii::t('app','Clients'), 'url' => ['index']];
  $this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
  $this->params['breadcrumbs'][] = Yii::t('app','Detail');
?>

<div id="app" data-student="<?= $model->id ?>"></div>

<?php
    $this->registerJsFile('/js/student/vendors.js',
    ['position' => yii\web\View::POS_END]);
    $this->registerJsFile('/js/student/app.js',
    ['position' => yii\web\View::POS_END]);
?>