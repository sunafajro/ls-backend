<?php

use app\models\StudentCommission;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var View              $this
 * @var StudentCommission $model
 * @var ActiveForm            $form
 * @var app\models\Student    $student
 */
?>
<div class="payment-form">
    <?php $form = ActiveForm::begin(); ?>
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
    <?= $form->field($model, 'debt')->textInput(['readonly' => true]) ?>
    <?= $form->field($model, 'percent')->textInput() ?>
    <?= $form->field($model, 'value')->textInput() ?>
    <?= $form->field($model, 'comment')->textArea() ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
$js = <<< 'SCRIPT'
$(document).ready(function() {
  $('#studentcommission-percent').on('change', function () {
      var _this = $(this);
      var percent = parseFloat(_this.val());
      var debt = parseFloat($('#studentcommission-debt').val());
      if (!isNaN(debt) && !isNaN(percent)) {
          var value = Math.round(debt * percent / 100);
          $('#studentcommission-value').val(Math.abs(value));
      }
  });
});
SCRIPT;
$this->registerJs($js);