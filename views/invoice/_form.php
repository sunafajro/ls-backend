<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CalcInvoicestud */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="invoice-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'calc_service')->dropDownList($items=$services, ['prompt'=>Yii::t('app','-select-')]) ?>

    <?php
        if($rubsales) { 
            echo $form->field($model, 'calc_salestud')->dropDownList($items=$rubsales, ['prompt'=>Yii::t('app','-select-')]);
        } 
        ?>

    <?php
        if($procsales) { 
            echo $form->field($model, 'calc_salestud_proc')->dropDownList($items=$procsales, ['prompt'=>Yii::t('app','-select-')]);
        }
    ?>
    <div class="row">
        <?php if ($permsale) : ?>
        <div class="col-sm-6">
            <?= Html::checkbox('Invoicestud[permsale]', true, ['id' => 'invoicestud-calc_permsale','label' => $permsale['name']]); ?>
        </div>
        <?php endif; ?>
        <div class="col-sm-6">
            <?= $form->field($model, 'remain')->checkbox(); ?>
        </div>  
    </div>    

    <div id="sale-alert" class="alert alert-success" style="display: none">
        При расчете стоимости счета будет учтена скидка <b>"<span id="sale-name"></span>"</b>!
    </div>

    <?= $form->field($model, 'num')->textInput() ?>

  	<?php
  	    if(Yii::$app->session->get('user.ustatus')==3) {
  			echo $form->field($model, 'calc_office')->dropDownList($items=$offices, ['prompt'=>Yii::t('app','-select-')]);
  		}
  	?>
	
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Create'), ['class' => 'btn btn-success']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>
</div>

<?php

$permsaleId = isset($permsale['id']) ? $permsale['id'] : 'null';
$permsaleName = isset($permsale['name']) ? $permsale['name'] : 'null';
$permsaleValue = isset($permsale['value']) ? $permsale['value'] : 'null';

$script = <<< JS
var service;
var num;
var rubsale;
var rubsaleName;
var procsale;
var procsaleName;
var permament;
var permsale = {
    id: $permsaleId,
    name: '$permsaleName',
    value: '$permsaleValue'
};
if ($("#invoicestud-calc_permsale")[0]) {
  permament = $("#invoicestud-calc_permsale")[0].checked;
}

if (permsale.id && permsale.name) {
  showSale(permsale.name);
}

$("#invoicestud-calc_permsale").change(function (e) {
  permament = e.target.checked;
  calculateSale();
});

$("#invoicestud-calc_service").change(function (e) {
  service = e.target.value;
  calculateSale();
});

$("#invoicestud-calc_salestud_proc").change(function (e) {
  procsale = e.target.value;
  procsaleName = $("#invoicestud-calc_salestud_proc option:selected").text();
  calculateSale();
});

$("#invoicestud-calc_salestud").change(function (e) {
  rubsale = e.target.value;
  rubsaleName = $("#invoicestud-calc_salestud option:selected").text();
  calculateSale();
});

$("#invoicestud-num").change(function (e) {
  num = e.target.value;
  calculateSale();
});

function showSale (name) {
  $('#sale-name').text(name);
  $('#sale-alert').show();
}

function hideSale () {
  $('#sale-alert').hide();
}

function calculateSale () {
  if (service && (rubsale || procsale) && num) {
    $.ajax({
      type:'POST',
      contentType: 'application/json',
      url:'/invoice/getsale',
      data: JSON.stringify({
        data: {
          serv: service,
          rub: rubsale ? rubsale : null,
          proc: procsale ? procsale : null,
          perm: permament ? permsale : null,
          num: num 
        }
      }),
      dataType: 'json',
      success: 
        function(result){
          if (result.sale) {
            switch(result.sale){
              case 'rub': showSale(rubsaleName); break;
              case 'proc': showSale(procsaleName); break;
              case 'perm': showSale(permsale.name); break;
              default: hideSale();
            }
          }
        }
    });
  } else {
    if (permament) {
      showSale(permsale.name);
    } else {
      hideSale();
    }
  }
}

JS;
$this->registerJs($script);

?>
