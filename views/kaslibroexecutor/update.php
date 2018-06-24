<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\KaslibroExecutor */

$this->title = 'Система учета :: ' . Yii::t('app','Update');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Expenses'), 'url' => ['kaslibro/index']];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Executors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id;
$this->params['breadcrumbs'][] = Yii::t('app','Update');
?>
<div class="kaslibro-executor-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
