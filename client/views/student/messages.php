<?php

/**
 * @var View    $this
 * @var array   $messages
 * @var Message $messageForm
 * @var array   $receivers
 */

use client\assets\MessageFormAsset;
use client\models\Message;
use client\models\MessageFile;
use client\models\TempFile;
use common\components\helpers\IconHelper;
use yii\helpers\Html;
use common\widgets\alert\AlertWidget;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ListView;
use yii\bootstrap\ActiveForm;

$this->title = Yii::$app->params['siteTitle'];
MessageFormAsset::register($this);
?>
<div class="content-block">
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <?= AlertWidget::widget() ?>
            <?= ListView::widget([
                'dataProvider' => $messages,
                'itemView' => '_messagesList',
                'layout' => "{items}\n{pager}",
                'pager' => [
                    'maxButtonCount' => 5,
                ],
            ]) ?>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <b><?= Yii::t('app', 'Send message')?></b>
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin([
                        'id' => 'send-message-form',
                    ]); ?>
                    <?= $form->field($messageForm, 'refinement_id')->dropDownList($receivers) ?>
                    <?= $form->field($messageForm, 'name')->textInput() ?>
                    <?= $form->field($messageForm, 'description')->textArea(['rows' => 5]) ?>
                    <div class="form-group js--files-block">
                        <div class="js--file-ids" data-delete-url="<?= Url::to(['files/delete']) ?>">
                            <?= Html::button(
                                IconHelper::icon('paperclip', null, null, 'fa5')
                                . ' '
                                . Yii::t('app', 'Attach file'), ['class' => 'btn btn-default btn-xs js--upload-file-btn', 'style' => 'margin-right: 5px']) ?>
                            <?php

                            $files = TempFile::find()->byUserId(Yii::$app->user->identity->id)->all();
                            if ($messageForm->id) {
                                $messageFiles = MessageFile::find()->byEntityId($messageForm->id)->byUserId(Yii::$app->user->identity->id)->all();
                                $files = array_merge($files, $messageFiles);
                            }
                            foreach ($files as $file) {
                                echo $this->render('_file_template', [
                                    'file' => $file,
                                    'model' => $messageForm,
                                ]);
                            }
                            ?>
                        </div>
                        <?= Html::input('file', 'file', null, ['class' => 'hidden js--upload-file', 'data-upload-url' => Url::to(['files/upload'])]) ?>
                    </div>
                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app', 'Send'), ['class' => 'btn btn-default']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                    <?= $this->render('_file_template', [
                            'file' => null,
                            'model' => $messageForm,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>