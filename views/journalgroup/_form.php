<?php

use app\models\Journalgroup;
use kartik\datetime\DateTimePicker;
use kartik\time\TimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var Journalgroup $model
 * @var ActiveForm $form
 * @var array $teachers
 */

$script = <<< JS
function updateEndTime(time) {
    if (typeof time === 'string' && time.length === 5) {
        var startTime = time.split(':');
        var endHour = parseInt(startTime[0], 10) + 1;
        endHour = endHour < 10 ? ('0' + endHour) : endHour;
        $("#js--lesson-end-time").val(endHour + ':' + startTime[1]);        
    }
}
$(".js--student-status").on('change', function() {
    var _this = $(this);
    _this.closest('.row').find('.js--comment-for-student').prop('required', _this.val() === '1');
});
$("#js--lesson-start-time").on('change', function (e) {
    updateEndTime(e.target.value);
});
JS;
$this->registerJs($script, yii\web\View::POS_READY);

$startTime = new \DateTime();
$endTime = new \DateTime();
$endTime->modify('+1 hour');
?>
<?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'data')->widget(DateTimePicker::class, [
        'options' => [
            'autocomplete' => 'off',
        ],
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
    ?>
    <?php if (count($teachers) > 1) { ?>
        <?= $form->field($model, 'calc_teacher')->dropDownList($teachers, ['options' => ['1' => ['selected' => true]]]) ?>
    <?php } ?>
    <?php
    if ((int)Yii::$app->session->get('user.ustatus') === 3 ||
        (int)Yii::$app->session->get('user.ustatus') === 4 ||
        (int)Yii::$app->session->get('user.uid') === 296 ||
        (int)Yii::$app->session->get('user.ustatus') === 10) { ?>
        <?= $form->field($model, 'calc_edutime')->dropDownList(Journalgroup::getEducationTimes(), ['options' => ['2' => ['selected' => true]]]) ?>
    <?php } ?>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'time_begin')->widget(TimePicker::class, [
                'id' => 'js--lesson-start-time',
                'pluginOptions' => [
                    'showMeridian' => false,
                    'defaultTime' => $model->isNewRecord ? $startTime->format('H:i') : $model->time_begin,
                ],
            ]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'time_end')->widget(TimePicker::class, [
                'id' => 'js--lesson-end-time',
                'pluginOptions' => [
                    'showMeridian' => false,
                    'defaultTime' => $model->isNewRecord ? $endTime->format('H:i') : $model->time_end,
                ],
            ]) ?>
        </div>
    </div>
    <?= $form->field($model, 'description')->textArea(['rows' => 3]) ?>
    <?= $form->field($model, 'homework')->textArea(['rows' => 3]) ?>
    <?php if ($model->isNewRecord && !empty($students)) {
        echo $this->render('_attendance', ['students' => $students]);
    } ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', $model->isNewRecord ? 'Add' : 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
<?php ActiveForm::end(); ?>
