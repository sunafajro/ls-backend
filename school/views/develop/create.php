<?php

/* @var $this yii\web\View */
/* @var $model school\models\Develop */

$this->title = Yii::t('app', 'Create request');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Bugtracker'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="develop-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
