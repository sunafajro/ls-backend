<?php

/**
 * @var View       $this
 * @var Moneystud  $model
 * @var ActiveForm $form
 * @var Student    $student
 * @var array      $offices
 */

use app\modules\school\assets\PaymentFormAsset;
use app\models\Moneystud;
use app\models\Student;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
use app\widgets\autocomplete\AutoCompleteWidget;

PaymentFormAsset::register($this);
?>
<div class="payment-form">
    <?php $form = ActiveForm::begin([
        'options' => [
            'data-office-search-url' => Url::to(['studname/offices']),
        ]
    ]); ?>
    <?php if (!$student) {
        echo AutoCompleteWidget::widget([
            'hiddenField' => [
                'name' => 'Moneystud[calc_studname]',
            ],
            'searchField' => [
                'label' => Yii::t('app', 'Student'),
                'url' => Url::to(['moneystud/autocomplete']),
                'minLength' => 1,
                'error' => $model->getFirstError('calc_studname'),
            ],
        ]);
    } ?> 
    <?php if ((int)Yii::$app->session->get('user.ustatus') === 11) { ?>
        <?= $form->field($model, 'value_cash')->hiddenInput(['value' => 0])->label(false) ?>
        <?= $form->field($model, 'value_card')->hiddenInput(['value' => 0])->label(false) ?>
        <?= $form->field($model, 'value_bank')->textInput(['value' => 0]) ?>
    <?php } else { ?>
        <?= $form->field($model, 'value_cash')->textInput(['value' => 0]) ?>
        <?= $form->field($model, 'value_card')->textInput(['value' => 0]) ?>
        <?= $form->field($model, 'value_bank')->textInput(['value' => 0]) ?>
    <?php } ?>
    <div class="alert alert-success"><b>Итог:</b> <span id="total_payment">0</span> р.</div>
    <?= $form->field($model, 'receipt')->textInput() ?>
    <?php if ((int)Yii::$app->session->get('user.ustatus') !== 4) { ?>
        <?= $form->field($model, 'calc_office')->dropDownList($offices, ['prompt' => Yii::t('app','-select-'), 'style' => 'font-family: FontAwesome, sans-serif']) ?>
    <?php } ?>
    <div class="form-group">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <?php if ($student) {
                    $hasEmail = preg_match('/.+@.+/', $student->email ?? '');
                    echo Html::checkbox(
                        'sendEmail',
                        $hasEmail === 1,
                        [
                            'disabled' => $hasEmail === 0,
                            'label' => Yii::t('app', 'Send notification') . ($hasEmail === 1 ? ' (' . $student->email . ')' : '')
                        ]
                    );
                } ?>
                <?= $form->field($model, 'remain')->checkbox(); ?>
            </div>
            <div class="col-xs-12 col-sm-6 text-right">
                <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
