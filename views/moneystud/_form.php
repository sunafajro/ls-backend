<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Moneystud */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'value_cash')->textInput(['value' => 0]) ?>

    <?= $form->field($model, 'value_card')->textInput(['value' => 0]) ?>

    <?= $form->field($model, 'value_bank')->textInput(['value' => 0]) ?>

    <div class="alert alert-success"><b>Итог:</b> <span id="total_payment">0</span> р.</div>

    <?= $form->field($model, 'receipt')->textInput() ?>
    
    <?= $form->field($model, 'remain')->checkbox(); ?>
    
    <?php
        // для руководителей даем возможность указать офис 
        if(Yii::$app->session->get('user.ustatus') == 3) {
            echo $form->field($model, 'calc_office')->dropDownList($items=$offices, ['prompt'=>Yii::t('app','-select-')]);
        }
     ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
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