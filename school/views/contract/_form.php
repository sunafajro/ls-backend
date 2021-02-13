<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use kartik\datetime\DateTimePicker;
?>

<div class="calc-contract-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'number')->textInput() ?>
    <?= $form->field($model, 'date')->widget(DateTimePicker::className(), [
        'pluginOptions' => [
            'language' => 'ru',
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
                'minView' => 2,
                'maxView' => 4,
                'weekStart' => 1,
                'autoclose' => true,
            ]
        ]);
    ?>
    <?= $form->field($model, 'signer')->textInput() ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Create'), ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>