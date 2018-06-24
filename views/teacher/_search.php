<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TeacherSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="calc-teacher-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'phone') ?>

    <?= $form->field($model, 'visible') ?>

    <?= $form->field($model, 'value_corp') ?>

    <?php // echo $form->field($model, 'accrual') ?>

    <?php // echo $form->field($model, 'fund') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'old') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'calc_statusjob') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
