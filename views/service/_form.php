<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
/* @var $this yii\web\View */
/* @var $model app\models\CalcService */
/* @var $form yii\widgets\ActiveForm */

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
        if($model->isNewRecord){
            echo $form->field($model, 'calc_servicetype')->dropDownList($items=$types, ['prompt' => Yii::t('app', '-select-')]);
            echo $form->field($model, 'calc_eduage')->dropDownList($items=$ages, ['prompt' => Yii::t('app', '-select-')]);
            echo $form->field($model, 'calc_lang')->dropDownList($items=$langs, ['prompt' => Yii::t('app', '-select-')]);
            echo $form->field($model, 'calc_eduform')->dropDownList($items=$forms, ['prompt' => Yii::t('app', '-select-')]);
            echo $form->field($model, 'calc_timenorm')->dropDownList($items=$norms, ['prompt' => Yii::t('app', '-select-')]);
        }
        
        echo $form->field($model, 'calc_city')->dropDownList($items=$city, ['prompt' => Yii::t('app', '-select-')]);

        echo $form->field($model, 'calc_studnorm')->dropDownList($items=$costs, ['prompt' => Yii::t('app', '-select-')]);
        

    ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
