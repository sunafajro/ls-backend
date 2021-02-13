<?php

use school\models\Book;
use school\models\OfficeBook;
use school\widgets\autocomplete\AutoCompleteWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var View       $this
 * @var OfficeBook $model
 * @var ActiveForm $form
 * @var array      $offices
 * @var array      $statuses
 */
?>
<div class="book-form">
    <?php $form = ActiveForm::begin(); ?>
    <?php if ($model->isNewRecord) { ?>
        <?= AutoCompleteWidget::widget([
            'hiddenField' => [
                'name' => Html::getInputName($model, 'book_id'),
            ],
            'searchField' => [
                'label' => Yii::t('app','Book'),
                'url' => Url::to(['office-book/autocomplete']),
                'minLength' => 1,
                'error' => NULL,
            ],
        ]) ?>
    <?php } else {
        $book = $model->book ?? null;
        if (!empty($book) && $book instanceof Book) {
            echo Html::beginTag('div', ['class' => 'form-group']);
            echo Html::tag('label', 'Учебник', ['class' => 'control-label']);
            echo Html::input('', '', "#{$book->id}, {$book->name} {$book->author}, {$book->isbn}", ['class' => 'form-control', 'disabled' => 'true']); 
            echo Html::endTag('div');
        }        
    } ?>
    <?= $form->field($model, 'office_id')->dropDownList($offices ?? [], [
        'prompt' => Yii::t('app', '-select-')
    ]) ?>
    <?= $form->field($model, 'serial_number')->textInput() ?>
    <?= $form->field($model, 'year')->textInput() ?>
    <?= $form->field($model, 'status')->dropDownList($statuses ?? [], [
        'prompt' => Yii::t('app', '-select-')
    ]) ?>
    <?= $form->field($model, 'comment')->textArea() ?>
    <div class="form-group">
        <?= Html::submitButton(
                !$model->id ? Yii::t('app', 'Create') : Yii::t('app','Update'),
                ['class' => !$model->id ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>