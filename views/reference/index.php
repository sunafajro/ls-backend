<?php
$this->title = 'Система учета :: ' . Yii::t('app','References');
$this->params['breadcrumbs'][] = Yii::t('app','References');
?>

<div id="references" class="reference-main-block">
  <router-view :links="links" :notify="notify" />
</div>

<?php
    $this->registerJsFile('/js/references/bundle.js',
    ['position' => yii\web\View::POS_END]);
?>