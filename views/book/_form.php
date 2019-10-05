<?php

use app\models\Book;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var View       $this
 * @var Book       $model
 * @var ActiveForm $form
 * @var array      $languages
 */
?>

<div class="calc-book-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput() ?>
    <?= $form->field($model, 'author')->textInput() ?>
    <?= $form->field($model, 'isbn')->textInput() ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'publisher')->textInput() ?>
    <?= $form->field($model, 'language_id')->dropDownList($languages ?? [], [
        'prompt' => Yii::t('app', '-select-')
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(
                $model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app','Update'),
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
