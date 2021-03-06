<?php

/**
 * @var View         $this
 * @var Journalgroup $model
 * @var ActiveForm   $form
 * @var int          $roleId
 * @var array        $teachers
 * @var array        $timeHints
 * @var int          $userId
 */

use school\models\Journalgroup;
use kartik\datetime\DateTimePicker;
use kartik\time\TimePicker;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
?>
<?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <?php
                try {
                    echo $form->field($model, 'data')->widget(DateTimePicker::class, [
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
                } catch (Exception $e) {
                    echo Html::tag('div', 'Неудалось отобразить виджет.', ['class' => 'alert alert-danger']);
                }
            ?>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <?= $form->field($model, 'type')
                    ->dropDownList(Journalgroup::getLessonLocationTypes(), ['prompt' => Yii::t('app', '-select-'), 'class' => 'form-control js--lesson-location-type']) ?>
        </div>
    </div>
    <?php if (count($teachers) > 1) { ?>
        <?= $form->field($model, 'calc_teacher')->dropDownList($teachers, ['options' => ['1' => ['selected' => true]]]) ?>
    <?php } ?>
    <?php
    if (in_array($roleId, [3, 4, 10]) || in_array($userId, [296, 389])) { ?>
        <?= $form->field($model, 'calc_edutime')->dropDownList(Journalgroup::getEducationTimes(), ['options' => $model->isNewRecord ? ['2' => ['selected' => true]] : []]) ?>
    <?php } ?>
    <div class="row">
        <div class="col-sm-4">
            <?php
                try {
                    echo $form->field($model, 'time_begin')->widget(TimePicker::class, [
                             'pluginOptions' => [
                                 'showMeridian' => false,
                                 'defaultTime' => $model->isNewRecord ? '' : $model->time_begin,
                             ],
                    ]);
                } catch (Exception $e) {
                    echo Html::tag('div', 'Неудалось отобразить виджет.', ['class' => 'alert alert-danger']);
                }
            ?>
        </div>
        <div class="col-sm-4">
            <?php
                try {
                    echo $form->field($model, 'time_end')->widget(TimePicker::class, [
                            'pluginOptions' => [
                                'showMeridian' => false,
                                'defaultTime' => $model->isNewRecord ? '' : $model->time_end,
                            ],
                    ]);
                } catch (Exception $e) {
                    echo Html::tag('div', 'Неудалось отобразить виджет.', ['class' => 'alert alert-danger']);
                }
            ?>
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
        echo $this->render('_attendance', [
            'students'  => $students,
            'isNew'     => true,
            'successes' => false,
        ]);
    } ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', $model->isNewRecord ? 'Add' : 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
<?php ActiveForm::end();
