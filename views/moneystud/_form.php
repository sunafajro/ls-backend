<?php

/**
 * @var yii\web\View          $this
 * @var app\models\Moneystud  $model 
 * @var ActiveForm            $form
 * @var string                $email
 * @var array                 $offices
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="payment-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'value_cash')->textInput(['value' => 0]) ?>
    <?= $form->field($model, 'value_card')->textInput(['value' => 0]) ?>
    <?= $form->field($model, 'value_bank')->textInput(['value' => 0]) ?>
    <div class="alert alert-success"><b>Итог:</b> <span id="total_payment">0</span> р.</div>
    <?= $form->field($model, 'receipt')->textInput() ?>
    <?php if ((int)Yii::$app->session->get('user.ustatus') === 3) { ?>
        <?= $form->field($model, 'calc_office')->dropDownList($offices, ['prompt' => Yii::t('app','-select-')]) ?>
    <?php } ?>
    <div class="form-group">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <?= Html::checkbox('sendEmail', $email ? true : false, ['disabled' => $email ? false : true, 'label' => Yii::t('app', 'Send notification') . ($email ? ' (' . $email . ')' : '')]) ?>    
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
$this->registerJs('
  function calculateTotalPayment() {
    var cache = prepareValues($("#moneystud-value_cash").val());
    var card  = prepareValues($("#moneystud-value_card").val());
    var bank  = prepareValues($("#moneystud-value_bank").val());
    $("#total_payment").text((cache + card + bank).toFixed(2));
  }
  function prepareValues(value) {
    value = value.replace(/,/gi, ".");
    value = parseFloat(value);
    value = Number.isNaN(value) ? 0 : value;
    return value;
  }
  $(document).ready(function() {
    $("#moneystud-value_cash").on("input", calculateTotalPayment);
    $("#moneystud-value_card").on("input", calculateTotalPayment);
    $("#moneystud-value_bank").on("input", calculateTotalPayment);
  });');
?>