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
<div class="row js--exam-contents"></div>
<?php ActiveForm::end(); ?>
<div id="template-input" class="col-xs-12 hidden">
  <?= Html::input('text', '', '', ['class' => 'form-control input-sm']) ?>
  <div class="help-block"></div>
</div>
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
  var contentsBlock = $('.js--exam-contents');
  contentsBlock.html('');
  if (e.target.value) {
    $.get('/student-grade/exam-contents?exam=' + e.target.value, {}, function(data) {
      console.log(data.hasOwnProperty('contents') && typeof(data.contents) === 'object' && Object.keys(data.contents).length)
      if (data.hasOwnProperty('contents') && typeof(data.contents) === 'object' && Object.keys(data.contents).length) {
        for(var key in data.contents) {
          var col = Object.keys(data.contents).length ? Math.floor(12 / Object.keys(data.contents).length) : null;
          var templateInput = $('#template-input').clone();
          if (col) {
            templateInput.addClass('col-sm-' + col);
          }
          templateInput.removeClass('hidden');
          var inputField = templateInput.find('input');
          inputField.prop('name', 'StudentGrade[contents][' + key + ']');
          inputField.prop('placeholder', data.contents[key]);
          contentsBlock.append(templateInput);
        }
      } else {
        var alertBlock = '<div class="alert alert-danger">Не удалось получить содержание экзамена</div>';
        contentsBlock.append(alertBlock);
      }
    });
  }
});
JS;
$this->registerJs($js);
?>