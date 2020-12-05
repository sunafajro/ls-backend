<?php

/**
 * @var \yii\web\View $this
 * @var \app\modules\school\models\News $model
 * @var \yii\widgets\ActiveForm $form
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use moonland\tinymce\TinyMCE;
?>
<div class="news-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>
	<?= $form->field($model, 'body')->widget(TinyMCE::class, [
        'toggle' => [
            'active' => false,
        ],
        'statusbar' => false,
        'menubar' => false,
        'paste_as_text' => true,
        'toolbar' => [
            'fontsizes',
            'bold',
            'italic',
            'underline',
            'alignleft',
            'aligncenter',
            'alignright',
            'alignjustify',
            'textcolor',
            'backgroundcolor',
            'bullist',
            'numlist',
            'link',
            'unlink',
            'image',
            'removeformat',
        ],
    ]);
	?>
    <div class="form-group">
        <?= Html::submitButton(
                $model->isNewRecord
                        ? Yii::t('app', 'Create')
                        : Yii::t('app', 'Update'),
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
