<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\KaslibroExecutor */

$this->title = 'Система учета :: ' . \Yii::t('app', 'Create');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Expenses'), 'url' => ['kaslibro/index']];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Executors'), 'url' => ['kaslibroexecutor/index']];
$this->params['breadcrumbs'][] = \Yii::t('app', 'Create');
?>
<div class="kaslibro-executor-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
