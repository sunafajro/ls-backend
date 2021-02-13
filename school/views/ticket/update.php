<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model school\models\Ticket */

$this->title = Yii::t('app', 'Update ticket');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tickets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calc-tickets-update">

    <?= $this->render('_form', [
        'model' => $model,
        'emps'=>$emps,
        'status'=>$status,
    ]) ?>

</div>