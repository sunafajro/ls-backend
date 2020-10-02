<?php

/**
 * @var View              $this
 * @var StudentCommission $model
 * @var ActiveForm        $form
 * @var Student           $student
 * @var array             $offices
 */

use app\modules\school\assets\StudentCommissionFormAsset;
use app\models\Student;
use app\models\StudentCommission;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

StudentCommissionFormAsset::register($this);
?>
<div class="payment-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'date')->widget(DateTimePicker::className(), [
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
    <?= $form->field($model, 'debt')->textInput(['readonly' => true]) ?>
    <?= $form->field($model, 'percent')->textInput() ?>
    <?= $form->field($model, 'value')->textInput() ?>
    <?= $form->field($model, 'comment')->textArea() ?>
    <?php if ((int)Yii::$app->session->get('user.ustatus') !== 4) { ?>
        <?= $form->field($model, 'office_id')->dropDownList($offices, ['prompt' => Yii::t('app','-select-'), 'style' => 'font-family: FontAwesome, sans-serif']) ?>
    <?php } ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
