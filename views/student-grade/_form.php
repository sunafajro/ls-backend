<?php
/**
 * @var $this          yii\web\View
 * @var $form          yii\widgets\ActiveForm
 * @var $model         app\models\StudentGrades
 * @var $gradeTypes
 * @var $studentId
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
?>
<?php $form = ActiveForm::begin([
  'action' => ['create', 'id' => $studentId],
  'method' => 'post',
]); ?>
<div class="row">
  <div class="col-xs-12 col-sm-1">
    <div class="form-group">
      <?= Html::submitButton('<i class="fa fa-plus" aria-hidden="true"></i>', ['class' => 'btn btn-success btn-sm btn-block']) ?>
    </div>
  </div>
  <div class="col-xs-12 col-sm-3">
    <?= $form->field($model, 'date')->widget(DateTimePicker::className(), [
      'options' => [
        'placeholder' => 'Дата аттестации',
      ],
      'size' => 'sm',
      'pluginOptions' => [
        'language' => 'ru',
        'format' => 'yyyy-mm-dd',
        'todayHighlight' => true,
        'minView' => 2,
        'maxView' => 4,
        'weekStart' => 1,
        'autoclose' => true,
      ]
    ])->label(false) ?>
  </div>          
  <div class="col-xs-12 col-sm-6">
    <?= $form->field($model, 'description')->textInput(['class' => 'form-control input-sm', 'placeholder' => Yii::t('app', 'Description')])->label(false) ?>
  </div>
  <div class="col-xs-12 col-sm-1">
    <?= $form->field($model, 'score')->textInput(['class' => 'form-control input-sm', 'placeholder' => Yii::t('app', 'Score')])->label(false) ?>
  </div>
  <div class="col-xs-12 col-sm-1">
    <?= $form->field($model, 'type')->dropDownList($items = $gradeTypes, ['class' => 'form-control input-sm'])->label(false) ?>
  </div>
</div>
<?php ActiveForm::end(); ?>