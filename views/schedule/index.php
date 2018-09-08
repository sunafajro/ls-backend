<?php
$this->title = 'Система учета :: ' . Yii::t('app', 'Schedule');
$this->params['breadcrumbs'][] = Yii::t('app','Schedule');
?>

<div id="app" class="row">
  <c-sidebar :user="user"></c-sidebar>
  <c-content></c-content>
</div>

<?php
    $this->registerCssFile('/css/schedule/vendors.css');
    $this->registerCssFile('/css/schedule/app.css');
    $this->registerJsFile('/js/schedule/vendors.js');
    $this->registerJsFile('/js/schedule/app.js');
?>