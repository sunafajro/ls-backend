<?php

/**
 * @var yii\web\View           $this
 * @var school\models\Salestud    $model
 * @var yii\widgets\ActiveForm $form
 * @var string                 $studentId
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use school\widgets\autocomplete\AutoCompleteWidget;
?>
<div class="salestud-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= AutoCompleteWidget::widget([
        'hiddenField' => [
            'name' => Html::getInputName($model, 'calc_sale'),
        ],
        'searchField' => [
            'label'     => Yii::t('app', 'Sale'),
            'url'       => Url::to(['salestud/autocomplete', 'sid' => $studentId]),
            'minLength' => 1,
            'error'     => $model->getFirstError('calc_sale'),
        ],
    ]) ?>
    <?= $form->field($model, 'reason')->textInput(['required' => true]) ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Add'), ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>