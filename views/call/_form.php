<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\CalcCall */
/* @var $form yii\widgets\ActiveForm */

$sex = [1=>Yii::t('app','Male'), 2=>Yii::t('app','Female')];

?>

<div class="calc-call-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'phone')->textInput() ?>

    <?= $form->field($model, 'email')->textInput() ?>

    <?= $form->field($model, 'calc_sex')->dropDownList($items=$sex,['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'calc_way')->dropDownList($items=$way,['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'calc_servicetype')->dropDownList($items=$servicetype,['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'calc_lang')->dropDownList($items=$language,['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'calc_edulevel')->dropDownList($items=$level,['prompt'=>Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'calc_eduage')->dropDownList($items=$age,['prompt'=>Yii::t('app', '-select-')]) ?>

    <?php
        // при редактировании запроса на обучение выводим список типов обучения и офисов
        if(!$model->isNewRecord && $model->calc_servicetype == 1) {
			echo "<div id='hidden-field'>";
            echo $form->field($model, 'calc_eduform')->dropDownList($items=$eduform,['prompt'=>Yii::t('app', '-select-')]);
			echo $form->field($model, 'calc_office')->dropDownList($items=$office,['prompt'=>Yii::t('app', '-select-')]);
			echo "</div>";
        }
		// для остальных случаях списки выводим в скрытом виде
        else {
			echo "<div id='hidden-field' style='display: none'>";
            echo $form->field($model, 'calc_eduform')->dropDownList($items=$eduform,['prompt'=>Yii::t('app', '-select-')]);
			echo $form->field($model, 'calc_office')->dropDownList($items=$office,['prompt'=>Yii::t('app', '-select-')]);
			echo "</div>";
        }
    ?>    

    <?php /*$form->field($model, 'calc_studname')->dropDownList($items=$student,['prompt'=>Yii::t('app', '-select-')])*/ ?>
	
	<?= $form->field($model, 'calc_studname')->widget(
        AutoComplete::className(), [
            'clientOptions' => [
                //'source' => $studdata,
				'source' =>Url::to(['call/autocomplete']),
				'minLength'=>'3',				
            ],
            'options'=>[
                'class'=>'form-control',
				'placeholder' => 'Начните набирать имя...',
            ]
        ]);
    ?>
	
	<?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app','Create') : Yii::t('app','Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>


<?php 

$this->registerJs('$(document).ready(
function(){
    $("#call-calc_servicetype").change(
        function(){
            var key = $("#call-calc_servicetype option:selected").val();
            if(key == 1) {
				$("#hidden-field").show();
			} else {
				$("#hidden-field").hide();
			}
        });
});');
?>

</div>
