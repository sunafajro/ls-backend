<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
/* @var $this yii\web\View */
/* @var $model app\models\CalcTicket */

$this->title = Yii::t('app', 'Close ticket');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tickets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calc-ticker-close">

    <?php $form = ActiveForm::begin(); ?>

	<?php
	    if($state == 2) {
			echo $form->field($model, 'data')->widget(DateTimePicker::className(), [
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
		} else {
			echo $form->field($model, 'calc_ticketstatus')->dropDownList($items=$status, ['prompt'=>Yii::t('app', '-select-')]);
		}
		?>
	
	<?php echo $form->field($model, 'comment')->textArea(['rows'=>5]); ?>
	
    <div class="form-group">
        <?php echo Html::submitButton($state==2 ? Yii::t('app', 'Adjourn') : Yii::t('app', 'Close'), ['class' => $state==2 ? 'btn btn-primary' : 'btn btn-danger']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>