<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
?>

<div class="teacherlangpremium-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'calc_langpremium')->dropDownList($items=$premiums, ['prompt'=>Yii::t('app', '-select-')]) ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Add'), ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
