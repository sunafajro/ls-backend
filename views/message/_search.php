<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CalcMessageSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="calc-message-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'longmess') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'description') ?>

    <?= $form->field($model, 'files') ?>

    <?php // echo $form->field($model, 'user') ?>

    <?php // echo $form->field($model, 'data') ?>

    <?php // echo $form->field($model, 'send') ?>

    <?php // echo $form->field($model, 'calc_messwhomtype') ?>

    <?php // echo $form->field($model, 'refinement') ?>

    <?php // echo $form->field($model, 'refinement_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
