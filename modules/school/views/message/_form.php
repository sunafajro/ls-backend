<?php

/**
 * @var View    $this
 * @var Message $model
 * @var array   $types
 * @var array   $receivers
 */

use app\modules\school\assets\MessageFormAsset;
use app\models\File;
use app\models\Message;
use moonland\tinymce\TinyMCE;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

MessageFormAsset::register($this);
?>
<div class="message-form">
    <?php $form = ActiveForm::begin();?>
    <?=$form->field($model, 'name')->textInput()?>
	<?=$form->field($model, 'description')->widget(TinyMCE::className(), [
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
    <?php
        if ($model->isNewRecord && !$model->calc_messwhomtype) {
            echo $form->field($model, 'calc_messwhomtype')->dropDownList(
                $types,
                [
                    'prompt'   => Yii::t('app', '-select-'),
                ]
            );
            echo $form->field($model, 'refinement_id')->hiddenInput(['value' => 0])->label(false);
        } else {
            echo Html::beginTag('div', ['class' => 'form-group']);
            echo Html::tag('label', $model->getAttributeLabel('calc_messwhomtype'), ['class' => 'control-label']);
            echo Html::input('text', null, $types[$model->calc_messwhomtype], ['class' => 'form-control', 'disabled' => true]);
            echo $form->field($model, 'calc_messwhomtype')->hiddenInput()->label(false);
            echo Html::endTag('div');
            if (!in_array($model->calc_messwhomtype, [5, 13]) && ($model->isNewRecord && !$model->refinement_id)) {
                echo $form->field($model, 'refinement_id')->hiddenInput(['value' => 0])->label(false);
            } else if (in_array($model->calc_messwhomtype, [5, 13]) && $model->refinement_id) {                
                echo Html::beginTag('div', ['class' => 'form-group']);
                echo Html::tag('label', $model->getAttributeLabel('refinement_id'), ['class' => 'control-label']);
                echo Html::input('text', null, $receivers[$model->refinement_id], ['class' => 'form-control', 'disabled' => true]);
                echo $form->field($model, 'refinement_id')->hiddenInput()->label(false);
                echo Html::endTag('div');
            }
        }
    ?>
    <div class="form-group js--files-block">
        <div class="js--file-ids" data-delete-url="<?= Url::to(['files/delete']) ?>">
            <?= Html::button(
                    Html::tag('i', null, ['class' => 'fa fa-paperclip', 'aria-hidden' => 'true'])
                    . ' '
                    . Yii::t('app', 'Attach file'), ['class' => 'btn btn-default btn-xs js--upload-file-btn', 'style' => 'margin-right: 5px']) ?>
            <?php
                $files = File::find()->andWhere(['user_id' => Yii::$app->user->identity->id])->andWhere([
                    'or',
                    ['entity_type' => File::TYPE_TEMP, 'entity_id' => null],
                    ['entity_type' => File::TYPE_ATTACHMENTS, 'entity_id' => $model->id ?? null]
                ])->all();
                foreach ($files as $file) {
                    echo $this->render('_file_template', [
                        'file' => $file,
                        'model' => $model,
                    ]);
                }
            ?>
        </div>
        <?= Html::input('file', 'file', null, ['class' => 'hidden js--upload-file', 'data-upload-url' => Url::to(['files/upload'])]) ?>
    </div>
    <div class="form-group">
        <?= Html::hiddenInput('send', 1, ['class' => 'js--send-now-input', 'disabled' => true]); ?>
        <?= Html::button(Yii::t('app', 'Send'), ['class' => 'btn btn-primary js--send-now-button', 'title' => 'Отправить немедленно'])?>
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success', 'title' => 'Сохранить, но не отправлять'])?>
    </div>
    <?php ActiveForm::end();?>
    <?= $this->render('_file_template', [
            'file' => null,
            'model' => $model,
        ]) ?>
</div>