<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Langtranslator */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="translator-language-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'calc_translationlangs')->dropDownList($item=$language, ['prompt'=>Yii::t('app', '-select-')]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Add'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
