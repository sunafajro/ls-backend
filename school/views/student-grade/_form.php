<?php

use common\components\helpers\ArrayHelper;
use school\models\Office;
use school\models\StudentGrade;
use school\models\Teacher;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;

/**
 * @var View         $this
 * @var ActiveForm   $form
 * @var StudentGrade $model
 * @var array        $exams
 * @var string       $studentId
 */
$teachers = Teacher::find()->select('name')->active()->indexBy('id')->orderBy('name')->column();
$teachers = ArrayHelper::unshiftOption($teachers, '-select teacher-');
$offices = Office::find()->select('name')->active()->indexBy('id')->orderBy('name')->column();
$offices = ArrayHelper::unshiftOption($offices, '-select office-');
?>
<?php $form = ActiveForm::begin([
  'action' => Url::to(['create', 'id' => $studentId]),
  'method' => 'post',
  'options' => ['class' => 'js--attestations-form'],
]); ?>
<div class="row">
  <div class="col-xs-12 col-sm-1">
    <div class="form-group">
      <?= Html::submitButton(
              Html::tag('i', '', ['class' => 'fa fa-plus', 'aria-hidden' => 'true']),
              [
                  'class' => 'btn btn-success btn-sm btn-block js--create-button',
              ]
          ) ?>
      <?= Html::submitButton(
              Html::tag('i', '', ['class' => 'fa fa-save', 'aria-hidden' => 'true']),
              [
                  'class' => 'btn btn-warning btn-sm btn-block js--update-button hidden',
                  'style' => 'margin-top: 0',
              ]
          ) ?>
    </div>
  </div>
  <div class="col-xs-12 col-sm-3">
      <?= $form->field($model, 'date')
            ->widget(DateTimePicker::class, [
                  'options' => [
                    'placeholder' => 'Дата аттестации',
                    'autocomplete' => 'off',
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
    <?= $form->field($model, 'description')
            ->dropDownList($exams, ['class' => 'form-control input-sm js--exam-select', 'data-url' => Url::to(['student-grade/exam-contents'])])
            ->label(false) ?>
  </div>
  <div class="col-xs-12 col-sm-2">
    <?= $form->field($model, 'score')->textInput(['class' => 'form-control input-sm', 'placeholder' => Yii::t('app', 'Score')])->label(false) ?>
  </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-6">
        <?= $form->field($model, 'teacher_id')
            ->dropDownList($teachers, ['class' => 'form-control input-sm js--teacher-select'])
            ->label(false) ?>
    </div>
    <div class="col-xs-12 col-sm-6">
        <?= $form->field($model, 'office_id')
            ->dropDownList($offices, ['class' => 'form-control input-sm js--office-select'])
            ->label(false) ?>
    </div>
</div>
<div class="row js--exam-contents" style="min-height: 40px"></div>
<?php ActiveForm::end(); ?>
<div id="template-input" class="col-xs-12 hidden">
  <?= Html::input('text', '', '', ['class' => 'form-control input-sm']) ?>
  <div class="help-block"></div>
</div>