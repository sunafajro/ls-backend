<?php

use app\models\Journalgroup;
use kartik\datetime\DateTimePicker;
use kartik\time\TimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var Journalgroup $model
 * @var ActiveForm   $form
 * @var array        $teachers
 * @var array        $timeHints
 */

$script = <<< JS
$(".js--previous-times").on('click', function () {
    $("#journalgroup-time_begin").val($(this).data('begin'));
    $("#journalgroup-time_end").val($(this).data('end'));
});
JS;
$this->registerJs($script, yii\web\View::POS_READY);
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
        <div class="col-sm-4">
            <?= $form->field($model, 'time_begin')->widget(TimePicker::class, [
                    'pluginOptions' => [
                        'showMeridian' => false,
                        'defaultTime' => $model->isNewRecord ? '' : $model->time_begin,
                    ],
            ]) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'time_end')->widget(TimePicker::class, [
                    'pluginOptions' => [
                        'showMeridian' => false,
                        'defaultTime' => $model->isNewRecord ? '' : $model->time_end,
                    ],
            ]) ?>
        </div>
        <div class="col-sm-4">
            <label class="control-label">Предыдущие занятия:</label>
            <div class="form-group">
                <?php
                    $result = [];
                    foreach ($timeHints ?? [] as $times) {
                        $result[] = Html::tag('h4',
                            Html::a(
                                $times['begin'] . ' - ' . $times['end'],
                                'javascript:void(0)',
                                [
                                    'class' => 'label label-default js--previous-times',
                                    'data' => [
                                        'begin' => $times['begin'],
                                        'end'   => $times['end'],
                                    ]
                                ]
                            ),
                            ['style' => 'float: left; margin: 5px']
                        );
                    }
                    echo !empty($result) ? join(' ', $result) : Html::tag('h4', 'Нет информации...');
                ?>
            </div>
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
