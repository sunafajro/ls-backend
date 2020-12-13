<?php

/**
 * @var View                 $this
 * @var UserTimeTrackingForm $model
 */

use app\components\helpers\IconHelper;
use app\modules\school\models\forms\UserTimeTrackingForm;
use app\modules\school\models\UserTimeTracking;
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
    <?= Html::submitButton(
        IconHelper::icon('plus') . ' ' . Yii::t('app', 'Add'),
        ['class' => 'btn btn-success btn-sm btn-block']
    ) ?>
</div>
<?php ActiveForm::end();