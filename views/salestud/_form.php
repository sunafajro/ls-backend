<?php

/**
 * @var yii\web\View           $this
 * @var app\models\Salestud    $model
 * @var yii\widgets\ActiveForm $form
 * @var string                 $studentId
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\widgets\autocomplete\AutoCompleteWidget;
?>
<div class="salestud-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= AutoCompleteWidget::widget([
        'hiddenField' => [
            'name' => 'Salestud[calc_sale]',
        ],
        'searchField' => [
            'label' => Yii::t('app', 'Sale'),
            'url' => Url::to(['salestud/autocomplete', 'sid' => $studentId]),
            'minLength' => 1,
            'error' => $model->getFirstError('calc_sale'),
        ],
    ]) ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Add'), ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>