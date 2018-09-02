<?php
$this->title = 'Система учета :: ' . Yii::t('app', 'Schedule');
$this->params['breadcrumbs'][] = Yii::t('app','Schedule');
?>

<div id="schedule-root" class="row">
  <c-sidebar :user="user"></c-sidebar>
  <c-content></c-content>
</div>

<?php
    $this->registerJsFile('/js/schedule/bundle.js',
    ['position' => yii\web\View::POS_END]);
?>