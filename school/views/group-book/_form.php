<?php

use school\models\GroupBook;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;

/**
 * @var View       $this
 * @var GroupBook  $model
 * @var ActiveForm $form
 * @var array      $books
 */
?>
<div class="group-book-form">
    <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'book_id')->dropDownList($books, ['prompt'=>Yii::t('app', '-select-')]) ?>
        <?= $form->field($model, 'primary')->checkbox(); ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Add') : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>