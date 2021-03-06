<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model school\models\CalcOrders */

$this->title = Yii::t('app', 'Create Calc Orders');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Calc Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calc-orders-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
