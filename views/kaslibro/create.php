<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Kaslibro */

$this->title = 'Система учета :: ' . Yii::t('app','Add');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Expenses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kaslibro-create">

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
