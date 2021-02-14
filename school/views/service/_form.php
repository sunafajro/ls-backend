<?php

/**
 * @var View       $this
 * @var Service    $model
 * @var ActiveForm $form
 * @var array      $ages
 * @var array      $cities
 * @var array      $costs
 * @var array      $forms
 * @var array      $langs
 * @var array      $norms
 * @var array      $types
 */

use school\models\Service;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
?>
<div class="service-form">
    <?php $form = ActiveForm::begin(); ?>
    <?php
        echo $form->field($model, 'data')->widget(DateTimePicker::className(), [
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
        echo $form->field($model, 'name')->textInput();
        echo $form->field($model, 'calc_servicetype')
            ->dropDownList($types, ['prompt' => Yii::t('app', '-select-'), 'disabled' => !$model->isNewRecord]);
        echo $form->field($model, 'calc_eduage')
            ->dropDownList($ages, ['prompt' => Yii::t('app', '-select-'), 'disabled' => !$model->isNewRecord]);
        echo $form->field($model, 'calc_lang')
            ->dropDownList($langs, ['prompt' => Yii::t('app', '-select-'), 'disabled' => !$model->isNewRecord]);
        echo $form->field($model, 'calc_eduform')
            ->dropDownList($forms, ['prompt' => Yii::t('app', '-select-'), 'disabled' => !$model->isNewRecord]);
        echo $form->field($model, 'calc_timenorm')
            ->dropDownList($norms, ['prompt' => Yii::t('app', '-select-'), 'disabled' => !$model->isNewRecord]);
        echo $form->field($model, 'calc_city')
            ->dropDownList($cities, ['prompt' => Yii::t('app', '-select-')]);
        echo $form->field($model, 'calc_studnorm')
            ->dropDownList($costs, ['prompt' => Yii::t('app', '-select-')]);
    ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
