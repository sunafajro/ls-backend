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
                'minLength' => 3,
                'error' => $model->getFirstError('calc_studname'),
            ],
        ]);
    } ?> 
    <?= $form->field($model, 'value_cash')->textInput(['value' => 0]) ?>
    <?= $form->field($model, 'value_card')->textInput(['value' => 0]) ?>
    <?php if ((int)Yii::$app->session->get('user.ustatus') === 11) { ?>
        <?= $form->field($model, 'value_bank')->hiddenInput(['value' => 0])->label(false) ?>
    <?php } else { ?>
        <?= $form->field($model, 'value_bank')->textInput(['value' => 0]) ?>
    <?php } ?>
    <div class="alert alert-success"><b>Итог:</b> <span id="total_payment">0</span> р.</div>
    <?= $form->field($model, 'receipt')->textInput() ?>
    <?php if ((int)Yii::$app->session->get('user.ustatus') !== 4) { ?>
        <?= $form->field($model, 'calc_office')->dropDownList($offices, ['prompt' => Yii::t('app','-select-')]) ?>
    <?php } ?>
    <div class="form-group">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <?php if ($student) {
                    echo Html::checkbox(
                        'sendEmail',
                        isset($student->email),
                        [
                            'disabled' => !isset($student->email),
                            'label' => Yii::t('app', 'Send notification') . (isset($student->email) ? ' (' . $student->email . ')' : '')
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