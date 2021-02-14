<?php

/**
 * @var View              $this
 * @var Book              $book
 * @var BookOrderPosition $model
 * @var ActiveForm        $form
 * @var array             $offices
 */

use school\assets\BookOrderPositionFormAsset;
use school\models\Book;
use school\models\BookOrderPosition;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

BookOrderPositionFormAsset::register($this);

$roleId = (int)Yii::$app->session->get('user.ustatus');
?>
<div class="book-order-position-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="form-group">
        <?= Html::tag('label', $book->getAttributeLabel('name')) ?>
        <?= Html::input('text', null, $book->name, ['class' => 'form-control', 'disabled' => true]) ?>
    </div>
    <div class="form-group">
        <?= Html::tag('label', $roleId !== 4 ? $model->getAttributeLabel('selling_cost_id') : Yii::t('app', 'Cost')) ?>
        <?= Html::input('text', null, $book->sellingCost->cost ?? 0, [
                'class'    => 'form-control',
                'disabled' => true,
                'id'       => 'js--book-cost-value',
            ]) ?>
    </div>
    <?= $form->field($model, 'count')->textInput() ?>
    <?= $form->field($model, 'paid')->textInput() ?>
    <?php if ($roleId !== 4) { ?>
        <?= $form->field($model, 'office_id')->dropDownList($offices, ['prompt' => Yii::t('app', '-select-')]) ?>
    <?php } ?>
    <div class="form-group">
        <?= Html::submitButton(
                $model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app','Update'),
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
