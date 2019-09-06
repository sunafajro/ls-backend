<?php

/**
 * @var yii\web\View          $this
 * @var app\models\Moneystud  $model
 * @var ActiveForm            $form
 * @var app\models\Student    $student
 * @var array                 $offices
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\widgets\autocomplete\AutoCompleteWidget;
?>
<div class="payment-form">
    <?php $form = ActiveForm::begin(); ?>
    <?php if (!$student) {
        echo AutoCompleteWidget::widget([
            'hiddenField' => [
                'name' => 'Moneystud[calc_studname]',
            ],
            'searchField' => [
                'label' => Yii::t('app', 'Student'),
                'url' => Url::to(['moneystud/autocomplete']),
                'minLength' => 1,
                'error' => $model->getFirstError('calc_studname'),
            ],
        ]);
    } ?> 
    <?php if ((int)Yii::$app->session->get('user.ustatus') === 11) { ?>
        <?= $form->field($model, 'value_cash')->hiddenInput(['value' => 0])->label(false) ?>
        <?= $form->field($model, 'value_card')->hiddenInput(['value' => 0])->label(false) ?>
        <?= $form->field($model, 'value_bank')->textInput(['value' => 0]) ?>
    <?php } else { ?>
        <?= $form->field($model, 'value_cash')->textInput(['value' => 0]) ?>
        <?= $form->field($model, 'value_card')->textInput(['value' => 0]) ?>
        <?= $form->field($model, 'value_bank')->textInput(['value' => 0]) ?>
    <?php } ?>
    <div class="alert alert-success"><b>Итог:</b> <span id="total_payment">0</span> р.</div>
    <?= $form->field($model, 'receipt')->textInput() ?>
    <?php if ((int)Yii::$app->session->get('user.ustatus') !== 4) { ?>
        <?= $form->field($model, 'calc_office')->dropDownList($offices, ['prompt' => Yii::t('app','-select-'), 'style' => 'font-family: FontAwesome, sans-serif']) ?>
    <?php } ?>
    <div class="form-group">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <?php if ($student) {
                    $hasEmail = preg_match('/.+@.+/', $student->email ?? '');
                    echo Html::checkbox(
                        'sendEmail',
                        $hasEmail === 1,
                        [
                            'disabled' => $hasEmail === 0,
                            'label' => Yii::t('app', 'Send notification') . ($hasEmail === 1 ? ' (' . $student->email . ')' : '')
                        ]
                    );
                } ?>
                <?= $form->field($model, 'remain')->checkbox(); ?>
            </div>
            <div class="col-xs-12 col-sm-6 text-right">
                <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
$js = <<< 'SCRIPT'
$(document).ready(function() {
  function calculateTotalPayment() {
    var cache = prepareValues($("#moneystud-value_cash").val());
    var card = prepareValues($("#moneystud-value_card").val());
    var bank = prepareValues($("#moneystud-value_bank").val());
    $("#total_payment").text((cache + card + bank).toFixed(2));
  }
  function prepareValues(value) {
    value = value.replace(/,/gi, ".");
    value = parseFloat(value);
    value = Number.isNaN(value) ? 0 : value;
    return value;
  }
  $("#moneystud-value_cash").on("input", calculateTotalPayment);
  $("#moneystud-value_card").on("input", calculateTotalPayment);
  $("#moneystud-value_bank").on("input", calculateTotalPayment);
});
SCRIPT;
$this->registerJs($js);

if ((int)Yii::$app->session->get('user.ustatus') === 11) {
$js = <<< 'SCRIPT'
$(document).ready(function() {
  var $officeListTemplate = $('#moneystud-calc_office').clone();
  $('#js--autocomplete-hidden').on('change', function () {
      var $this = $(this);
      $.ajax({
        method: "GET",
        url: "/studname/offices?id=" + $this.val(),
      }).done(function(result) {
        $('#moneystud-calc_office').html('');
        $('#moneystud-calc_office').append($officeListTemplate.find('option').clone());
        $('#moneystud-calc_office').val(null);
        if (Array.isArray(result) && result.length) {
          $selectOfficeElement = $('#moneystud-calc_office');
          var mainOfficeId = null;
          result.forEach(function (item) {
            var $option = $('#moneystud-calc_office').find('option[value="' + item.id + '"]');
            if ($option.length === 1) {
                $option.html((item.isMain === '1' ? '&#xf005;' : '&#xf006') + ' ' + $option.text());
            }
            if (item.isMain === '1') {
                mainOfficeId = item.id;
            }
          });
          if (mainOfficeId) {
            var $option = $('#moneystud-calc_office').find('option[value="' + mainOfficeId + '"]');
            if ($option.length === 1) {
              $selectOfficeElement.val(mainOfficeId);
            }
          } else {
            var $option = $('#moneystud-calc_office').find('option[value="' + result[0].id + '"]');
            if ($option.length === 1) {
              $selectOfficeElement.val(result[0].id);
            }  
          }
        }
      });
  });
});
SCRIPT;
$this->registerJs($js);
}
