<?php

/**
 * @var View  $this
 * @var Sale  $model
 * @var array $types
 */

use app\assets\SaleFormAsset;
use app\models\Sale;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;

SaleFormAsset::register($this);
?>
<div class="discount-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'name')->textInput() ?>
    <?php if ($model->isNewRecord) { ?>
        <?= $form->field($model, 'procent')->dropDownList($types, ['prompt' => Yii::t('app', '-select-')]) ?>
        <?= $form->field($model, 'value')->textInput() ?>
        <div id="sale-base-block" style="display: none">
            <?= $form->field($model, 'base')->textInput() ?>
        </div>
    <?php } ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
