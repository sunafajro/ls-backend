<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
//use yii\jui\AutoComplete;
//use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Kaslibro */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="kaslibro-form">

    <?php $form = ActiveForm::begin(); ?>

	<?php
	/*
	    echo $form->field($model, 'date')->widget(DateTimePicker::className(), [
			'pluginOptions' => [
			    'language' => 'ru',
				'format' => 'yyyy-mm-dd',
				'todayHighlight' => true,
				'minView' => 2,
				'maxView' => 2,
				'weekStart' => 1,
				'autoclose' => true,
			]
    ]);
    */
	?>

    <?= $form->field($model, 'operation')->dropDownList($items=$operations, ['prompt' => \Yii::t('app', '-select-')]) ?>

    <?= $form->field($model, 'operation_detail')->textInput(['maxlength' => true]) ?>
	
    <?= $form->field($model, 'client')->dropDownList($items=$clients, ['prompt' => \Yii::t('app', '-select-')]) ?>
	
	<?php
	    /*
	    $form->field($model, 'client')->widget(
        AutoComplete::className(), [
            'clientOptions' => [
				'source' =>Url::to(['kaslibro/clientauto']),
				'minLength'=>'3',				
            ],
            'options'=>[
                'class'=>'form-control',
				'placeholder' => 'Начните набирать...',
            ]
        ]);
        */
    ?>

    <?= $form->field($model, 'executor')->dropDownList($items=$executors, ['prompt' => \Yii::t('app', '-select-')]) ?>
<div class="row">
    <div class="col-sm-6">
    <?= $form->field($model, 'month')->dropDownList($items=$months, ['prompt' => \Yii::t('app', '-select-')]) ?>
    </div>
    <div class="col-sm-6">
	<?= $form->field($model, 'year')->dropDownList($items=$years, ['prompt' => \Yii::t('app', '-select-')]) ?>
    </div>
</div>
    <?php
	    if(\Yii::$app->session->get('user.ustatus')!=4) {
	        echo $form->field($model, 'office')->dropDownList($items=$offices, ['prompt' => \Yii::t('app', '-select-')]);
		}
	 ?>
    <?= $form->field($model, 'code')->dropDownList($items=$codes, ['prompt' => \Yii::t('app', '-select-')]) ?>
<div class="row">
    <div class="col-sm-6">
    <?= $form->field($model, 'n_plus')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
    <?= $form->field($model, 'n_minus')->textInput(['maxlength' => true]) ?>
    </div>
</div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app','Create') : Yii::t('app','Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
