<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\time\TimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\CalcSchedule */
/* @var $form yii\widgets\ActiveForm */
?>

    <?php $form = ActiveForm::begin(); ?>

    <?php
        echo $form->field($model, 'calc_teacher')->dropDownList($items=$teachers,['prompt'=> Yii::t('app','-select-')]);
		
    ?>

    <?php 
    if($model->isNewRecord){
        echo $form->field($model, 'calc_groupteacher')->hiddenInput()->label(false);
		//echo $form->field($model, 'calc_groupteacher')->dropDownList($items=[],['id' => 'office', 'prompt' => Yii::t('app','-select-'), 'disabled' => true, 'class' => 'form-control form-control-sm']);
    }
    else{
        echo $form->field($model, 'calc_groupteacher')->dropDownList($items=$groups,['prompt'=> Yii::t('app','-select-')]);
    }
    ?>

    <?php

        echo $form->field($model, 'calc_office')->dropDownList($items=$offices,['prompt'=> Yii::t('app','-select-')]);

    ?>
    
    <?php
    if($model->isNewRecord){
        echo $form->field($model, 'calc_cabinetoffice')->hiddenInput()->label(false);
		//echo $form->field($model, 'calc_cabinetoffice')->dropDownList($items=[],['id' => 'office', 'prompt' => Yii::t('app','-select-'), 'disabled' => true, 'class' => 'form-control form-control-sm']);
    }
    else{
        echo $form->field($model, 'calc_cabinetoffice')->dropDownList($items=$cabinets,['prompt'=> Yii::t('app','-select-')]);
    }
    ?>

    <?php /* $form->field($model, 'time_begin')->dropDownList($items=$time,['prompt'=>\Yii::t('app','-select-')]); */ ?>
	
	<?php
	    echo $form->field($model, 'time_begin')->widget(TimePicker::className(), [
			'pluginOptions' => [
                'showMeridian' => false,
                'minuteStep' => 5,
			]
    ]);
	?>

	<?php
	    echo $form->field($model, 'time_end')->widget(TimePicker::className(), [
			'pluginOptions' => [
                'showMeridian' => false,
                'minuteStep' => 5,
			]
    ]);
	?>
	
    <?= $form->field($model, 'calc_denned')->dropDownList($items=$days,['prompt' => Yii::t('app','-select-')]) ?>
	
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app','Create') : Yii::t('app','Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

<?php 

$this->registerJs('$(document).ready(
    function(){
        $("#schedule-calc_teacher").change(
            function(){
                var g = $("#schedule-calc_teacher option:selected").val();
                $.ajax({type:"POST", url:"/schedule/ajaxgroup", data: "TID="+g, success: function(teachergroup){
                    $(".field-schedule-calc_groupteacher").html(teachergroup);
                }
            });
        });
        $("#schedule-calc_office").change(
            function(){
                var k = $("#schedule-calc_office option:selected").val();
                $.ajax({type:"POST", url:"/schedule/ajaxgroup", data: "OID="+k, success: function(officecabinet){
                    $(".field-schedule-calc_cabinetoffice").html(officecabinet);
                }
            });
        });
    });');
?>
