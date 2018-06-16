<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CalcOrdersSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="calc-orders-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'number') ?>

    <?= $form->field($model, 'content') ?>

    <?= $form->field($model, 'calc_messwhomtype') ?>

    <?php // echo $form->field($model, 'date') ?>

    <?php // echo $form->field($model, 'user') ?>

    <?php // echo $form->field($model, 'approve1') ?>

    <?php // echo $form->field($model, 'approve2') ?>

    <?php // echo $form->field($model, 'published') ?>

    <?php // echo $form->field($model, 'visible') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
