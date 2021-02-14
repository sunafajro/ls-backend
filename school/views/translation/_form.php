<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model school\models\Translation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="translation-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'data')->widget(DateTimePicker::className(), [
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


    <?= $form->field($model, 'calc_translationclient')->dropDownList($items=$client, ['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'calc_translator')->dropDownList($items=$translator, ['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'from_language')->dropDownList($items=$language, ['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'to_language')->dropDownList($items=$language, ['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'nomination')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'calc_translationnorm')->dropDownList($items=$norm, ['prompt'=>Yii::t('app', '-select-')]) ?>

    <?php
    if($model->isNewRecord){
        echo $form->field($model, 'printsymbcount')->textInput(['value'=>0]);
        echo $form->field($model, 'accunitcount')->textInput(['value'=>0]);
        echo $form->field($model, 'value_correction')->textInput(['value'=>0]);
    } else {
        echo $form->field($model, 'printsymbcount')->textInput();
        echo $form->field($model, 'accunitcount')->textInput();
        echo $form->field($model, 'value_correction')->textInput();
        echo $form->field($model, 'data_end')->widget(DateTimePicker::className(), [
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
        echo $form->field($model, 'receipt')->textInput(['maxlength' => true]);
    } ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
