<?php
/**
 * @var View    $this
 * @var Student $model
 * @var array   $sex
 * @var array   $way
 */

use school\assets\AddressAutocompleteAsset;
use school\models\Student;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;

AddressAutocompleteAsset::register($this);
?>
<div class="calc-studname-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'lname')->textInput() ?>
    <?= $form->field($model, 'fname')->textInput() ?>
    <?= $form->field($model, 'mname')->textInput() ?>
    <?= $form->field($model, 'birthdate')->widget(DateTimePicker::class, [
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
    <?= $form->field($model, 'email')->textInput() ?>
    <?= $form->field($model, 'phone')->textInput() ?>
    <?= $form->field($model, 'address')->textInput(['maxlength' => true, 'class' => 'form-control js--address-field']) ?>
    <?= $form->field($model, 'calc_sex')->dropDownList($sex, ['prompt'=>Yii::t('app', '-select-')]) ?>
    <?= $form->field($model, 'calc_way')->dropDownList($way, ['prompt'=>Yii::t('app', '-select-')]) ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app','Create') : Yii::t('app','Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
