<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model school\models\Kaslibro */

$this->title = 'Система учета :: ' . Yii::t('app','Update');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Expenses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->id;
$this->params['breadcrumbs'][] = Yii::t('app','Update');
?>
<div class="kaslibro-update">

    <?= $this->render('_form', [
        'model' => $model,
        'operations' => $operations,
        'clients' => $clients,
        'executors' => $executors,
        'offices' => $offices,
        'months' => $months,
        'codes' => $codes,
        'moffice' => $moffice,
        'years' => $years,
    ]) ?>

</div>
