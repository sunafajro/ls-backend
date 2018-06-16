<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CalcTicket */

$this->title = Yii::t('app', 'Create ticket');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tickets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calc-tickets-create">

    <?= $this->render('_form', [
        'model' => $model,
        //'emps'=>$emps,
    ]) ?>

</div>