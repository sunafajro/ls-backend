<?php

/**
 * @var View       $this
 * @var BookForm   $model
 * @var ActiveForm $form
 * @var array      $languages
 */

use school\models\forms\BookForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
?>
<div class="book-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput() ?>
    <?= $form->field($model, 'author')->textInput() ?>
    <?= $form->field($model, 'isbn')->textInput() ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'publisher')->textInput() ?>
    <?= $form->field($model, 'language_id')->dropDownList($languages ?? [], [
        'prompt' => Yii::t('app', '-select-')
    ]) ?>
    <?= $form->field($model, 'purchase_cost')->textInput() ?>
    <?= $form->field($model, 'selling_cost')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(
                !$model->id ? Yii::t('app', 'Create') : Yii::t('app','Update'),
                ['class' => !$model->id ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>