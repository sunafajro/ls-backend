<?php
/**
 * @var yii\web\View             $this
 * @var yii\widgets\ActiveForm   $form
 * @var app\models\StudentGrades $model
 * @var array                    $exams
 * @var string                   $studentId
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
?>
<?php $form = ActiveForm::begin([
  'action' => ['create', 'id' => $studentId],
  'method' => 'post',
  'options' => [
    'class' => 'js--student-grade-form'
  ]
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
    <?= $form->field($model, 'description')->dropDownList($items = $exams, ['class' => 'form-control input-sm js--exam-select'])->label(false) ?>
  </div>
  <div class="col-xs-12 col-sm-2">
    <?= $form->field($model, 'score')->textInput(['class' => 'form-control input-sm', 'placeholder' => Yii::t('app', 'Score')])->label(false) ?>
  </div>
</div>
<div class="row js--exam-contents-first hidden">
    <div class="col-xs-12 col-sm-4">
      <div class="form-group">
        <?= Html::input('text', 'StudentGradeContents[listening]', null, ['class' => 'form-control input-sm', 'placeholder' => 'Listening']) ?>
      </div>
    </div>
    <div class="col-xs-12 col-sm-4">
      <div class="form-group">
        <?= Html::input('text', 'StudentGradeContents[readingAndWriting]', null, ['class' => 'form-control input-sm', 'placeholder' => 'Reading & Writing']) ?>
      </div>
    </div>
    <div class="col-xs-12 col-sm-4">
      <div class="form-group">
        <?= Html::input('text', 'StudentGradeContents[speaking]', null, ['class' => 'form-control input-sm', 'placeholder' => 'Speaking']) ?>
      </div>
    </div>
</div>
<div class="row js--exam-contents-second hidden">
    <div class="col-xs-12 col-sm-2">
      <div class="form-group">
        <?= Html::input('text', 'StudentGradeContents[listening]', null, ['class' => 'form-control input-sm', 'placeholder' => 'Listening']) ?>
      </div>
    </div>
    <div class="col-xs-12 col-sm-2">
      <div class="form-group">
        <?= Html::input('text', 'StudentGradeContents[reading]', null, ['class' => 'form-control input-sm', 'placeholder' => 'Reading']) ?>
      </div>
    </div>
    <div class="col-xs-12 col-sm-2">
      <div class="form-group">
        <?= Html::input('text', 'StudentGradeContents[useOfEnglish]', null, ['class' => 'form-control input-sm', 'placeholder' => 'Use of English']) ?>
      </div>
    </div>
    <div class="col-xs-12 col-sm-2">
      <div class="form-group">
        <?= Html::input('text', 'StudentGradeContents[writing]', null, ['class' => 'form-control input-sm', 'placeholder' => 'Writing']) ?>
      </div>
    </div>
    <div class="col-xs-12 col-sm-2">
      <div class="form-group">
        <?= Html::input('text', 'StudentGradeContents[speaking]', null, ['class' => 'form-control input-sm', 'placeholder' => 'Speaking']) ?>
      </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php
$js = <<<JS
function hideCOmponent(content) {
  var contentBlock = $(content);
  if (!contentBlock.hasClass('hidden')) {
    contentBlock.toggleClass('hidden');
    var fields = contentBlock.find('input');
    fields.each(function(el) {
      fields[el].value = '';
    });
  }
}
function showCOmponent(content) {
  var contentBlock = $(content);
  if (contentBlock.hasClass('hidden')) {
    contentBlock.toggleClass('hidden');
  }
}
$('.js--exam-select').on('change', function(e) {
  if (e.target.value) {
    $.get('/student-grade/exam-contents?exam=' + e.target.value, {}, function(data) {
      if (data) {
        if (data.hasOwnProperty('show')) {
          showCOmponent(data.show);
        }
        if (data.hasOwnProperty('hide')) {
          hideCOmponent(data.hide);
        }
      }
    });
  } else {
    hideCOmponent('.js--exam-contents-first');
    hideCOmponent('.js--exam-contents-second');
  }
});
$('.js--student-grade-form').on('submit', function () {
  if ($('.js--exam-contents-first').hasClass('hidden')){
    $('.js--exam-contents-first').html('');
  }
  if ($('.js--exam-contents-second').hasClass('hidden')){
    $('.js--exam-contents-second').html('');
  }
})
JS;
$this->registerJs($js);
?>