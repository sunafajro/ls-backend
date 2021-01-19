<?php
/**
 * @var yii\web\View       $this
 * @var app\models\Student $model
 * @var array              $sex
 * @var array              $way
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
?>

<div class="calc-studname-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'lname')->textInput() ?>
    <?= $form->field($model, 'fname')->textInput() ?>
    <?= $form->field($model, 'mname')->textInput() ?>
    <?= $form->field($model, 'birthdate')->widget(DateTimePicker::className(), [
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
    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'calc_sex')->dropDownList($items=$sex, ['prompt'=>Yii::t('app', '-select-')]) ?>
    <?= $form->field($model, 'calc_way')->dropDownList($items=$way, ['prompt'=>Yii::t('app', '-select-')]) ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app','Create') : Yii::t('app','Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php $this->registerJs('
$(document).ready(
  function(){
    $("#student-address").suggestions({
        token: "9ac43b0c02b76d2f8be18c637ce94133d7c66e7f",
        type: "ADDRESS",
        count: 5,
        onSelect: function(suggestion) {
          console.log(suggestion);
        }
      });
  });
');
?>