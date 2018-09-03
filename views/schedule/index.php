<?php
$this->title = 'Система учета :: ' . Yii::t('app', 'Schedule');
$this->params['breadcrumbs'][] = Yii::t('app','Schedule');
?>

<div id="app" class="row">
  <c-sidebar :user="user"></c-sidebar>
  <c-content></c-content>
</div>

<?php
    $this->registerCssFile('/css/chunk-vendors.f39c34b7.css');
    $this->registerCssFile('/css/app.1cf9e0a6.css');
    $this->registerJsFile('/js/chunk-vendors.5aa52ffe.js');
    $this->registerJsFile('/js/app.c76b92a9.js');
?>