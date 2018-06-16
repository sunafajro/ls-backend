<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList($items=$statuses,['prompt' => Yii::t('app','-select-')]) ?>

    <?php
    if(!$model->isNewRecord && $model->status == 4){
        echo $form->field($model, 'calc_office')->dropDownList($items=$offices,['prompt' => Yii::t('app','-select-')]);    
        echo $form->field($model, 'calc_city')->dropDownList($items=$cities,['prompt' => Yii::t('app','-select-')]);
    } else {
        echo $form->field($model, 'calc_office')->dropDownList($items=$offices,['prompt' => Yii::t('app','-select-'), 'disabled' => true]);    
        echo $form->field($model, 'calc_city')->dropDownList($items=$cities,['prompt' => Yii::t('app','-select-'), 'disabled' => true]);
    }
    ?>

    <?= $form->field($model, 'calc_teacher')->dropDownList($items=$teachers,['prompt' => Yii::t('app','-select-')]) ?>

    <?= $form->field($model, 'login')->textInput() ?>

    <?php
    if($model->isNewRecord){ 
        echo $form->field($model, 'pass')->passwordInput();
    }
    ?>
   
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app','Add') : Yii::t('app','Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php 

$this->registerJs('
$(document).ready(
  function(){
    $("#user-status").change(
      function(){
        if($("#user-status option:selected").val() == 4) {
            $("#user-calc_city").prop("disabled", false);
            $("#user-calc_office").prop("disabled", false);
        } else {
            $("#user-calc_city").prop("selectedIndex",0);
            $("#user-calc_office").prop("selectedIndex",0);
            $("#user-calc_city").prop("disabled", true);
            $("#user-calc_office").prop("disabled", true);
        }
      }
    );
  }
);
');

?>