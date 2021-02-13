<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model school\models\CalcLangteacherSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="calc-langteacher-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'calc_teacher') ?>

    <?= $form->field($model, 'calc_lang') ?>

    <?= $form->field($model, 'visible') ?>

    <?= $form->field($model, 'data') ?>

    <?php // echo $form->field($model, 'user') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
