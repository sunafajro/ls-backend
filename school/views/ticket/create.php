<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model school\models\Ticket */

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