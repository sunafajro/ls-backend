<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CalcBook */

$this->title = 'Update Calc Book: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Calc Books', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="calc-book-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
