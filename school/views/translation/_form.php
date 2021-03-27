<?php

/**
 * @var View $this
 * @var Translation $model
 * @var ActiveForm $form
 * @var array $clients
 * @var array $translators
 * @var array $languages
 * @var array $norms
 */

use school\models\Translation;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
?>
<div class="translation-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'data')->widget(DateTimePicker::class, [
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

    <?= $form->field($model, 'calc_translationclient')->dropDownList($clients, ['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'calc_translator')->dropDownList($translators, ['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'from_language')->dropDownList($languages, ['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'to_language')->dropDownList($languages, ['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'nomination')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'calc_translationnorm')->dropDownList($norms, ['prompt'=>Yii::t('app', '-select-')]) ?>

    <?php
    if($model->isNewRecord){
        echo $form->field($model, 'printsymbcount')->textInput(['value'=>0]);
        echo $form->field($model, 'accunitcount')->textInput(['value'=>0]);
        echo $form->field($model, 'value_correction')->textInput(['value'=>0]);
    } else {
        echo $form->field($model, 'printsymbcount')->textInput();
        echo $form->field($model, 'accunitcount')->textInput();
        echo $form->field($model, 'value_correction')->textInput();
        echo $form->field($model, 'data_end')->widget(DateTimePicker::class, [
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
