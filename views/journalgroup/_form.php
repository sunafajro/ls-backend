<?php
/* !!! форма изменения существующего занятия !!! */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\CalcJournalgroup */
/* @var $form yii\widgets\ActiveForm */
?>
    <?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'data')->widget(DateTimePicker::className(), [
			'pluginOptions' => [
			    'language' => 'ru',
				'format' => 'yyyy-mm-dd',
				'todayHighlight' => true,
				'minView' => 2,
				'maxView' => 2,
				'weekStart' => 1,
				'autoclose' => true,
			]
    ]); ?>

    <?php if(count($teachers) > 1): ?>
        <?= $form->field($model, 'calc_teacher')->dropDownList($items = $teachers) ?>
    <?php endif; ?>
    
    <?php
	    if((int)Yii::$app->session->get('user.ustatus') === 3 ||
	       (int)Yii::$app->session->get('user.ustatus') === 4 ||
	       (int)Yii::$app->session->get('user.ustatus') === 10) {
	        echo $form->field($model, 'calc_edutime')->dropDownList($items = $times);
		}
	?>

    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'homework')->textarea(['rows' => 3]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
