<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
/* @var $this yii\web\View */
/* @var $model app\models\CalcTicket */
/* @var $form yii\widgets\ActiveForm */

//$types = [1 => 'Система учета', 2 => 'Личный кабинет студента'];
?>

<div class="calc-orders-form">

    <?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'title')->textInput() ?>
	
    <?= $form->field($model, 'body')->textArea(['rows'=>5]) ?>

	<?php
		echo $form->field($model, 'deadline')->widget(DateTimePicker::className(), [
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

    <?php
	    if(!$model->isNewRecord) {
			echo $form->field($model, 'calc_ticketstatus')->dropDownList($items=$status, ['prompt'=>Yii::t('app', '-select-')]);
		}
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
