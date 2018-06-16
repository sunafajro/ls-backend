<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CalcBookSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="calc-book-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'author') ?>

    <?= $form->field($model, 'isbn') ?>

    <?= $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'user') ?>

    <?php // echo $form->field($model, 'data') ?>

    <?php // echo $form->field($model, 'visible') ?>

    <?php // echo $form->field($model, 'calc_bookpublisher') ?>

    <?php // echo $form->field($model, 'calc_lang') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
