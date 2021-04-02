<?php

/**
 * @var View                 $this
 * @var UserTimeTrackingForm $model
 * @var int                  $userId
 */

use common\components\helpers\IconHelper;
use school\models\forms\UserTimeTrackingForm;
use school\models\UserTimeTracking;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
?>
<?php $form = ActiveForm::begin(); ?>
<?php
$datePickerOptions = [
    'options' => [
        'autocomplete' => 'off',
    ],
    'pluginOptions' => [
        'language' => 'ru',
        'format' => 'dd.mm.yyyy hh:ii',
        'todayHighlight' => true,
        'maxView' => 2,
        'weekStart' => 1,
        'autoclose' => true,
    ]
];
try {
    echo $form->field($model, 'start')->widget(DateTimePicker::class, $datePickerOptions);
} catch (Exception $e) {
    echo Html::tag('div', 'Неудалось отобразить виджет.', ['class' => 'alert alert-danger']);
}
?>
<?php
try {
    echo $form->field($model, 'end')->widget(DateTimePicker::class, $datePickerOptions);
} catch (Exception $e) {
    echo Html::tag('div', 'Неудалось отобразить виджет.', ['class' => 'alert alert-danger']);
}
?>
<?= $form->field($model, 'type')->dropDownList(UserTimeTracking::getTypeLabels(), ['prompt' => Yii::t('app', '-select-')]) ?>
<?= $form->field($model, 'comment')->textarea(['rows' => 3]) ?>
<div class="form-group">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
            <?= Html::submitButton(
                IconHelper::icon(!$model->id ? 'plus' : 'save', Yii::t('app', !$model->id ? 'Add' : 'Update')),
                ['class' => 'btn btn-success btn-sm btn-block', 'style' => 'margin-bottom:1rem']
            ) ?>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
            <?= Html::a(
                IconHelper::icon('eraser', Yii::t('app', 'Clear')),
                ['user/time-tracking', 'id' => $userId],
                ['class' => 'btn btn-warning btn-sm btn-block', 'style' => 'margin-bottom:1rem']
            ) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end();