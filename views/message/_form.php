<?php

use app\models\Message;
use moonland\tinymce\TinyMCE;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var View    $this
 * @var Message $model
 * @var array   $types
 * @var array   $receivers
 */
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
    <div class="form-group">
        <?= Html::hiddenInput('send', 1, ['class' => 'js--send-now-input', 'disabled' => true]); ?>
        <?= Html::button(Yii::t('app', 'Send'), ['class' => 'btn btn-primary js--send-now-button', 'title' => 'Отправить немедленно'])?>
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success', 'title' => 'Сохранить, но не отправлять'])?>
    </div>
    <?php ActiveForm::end();?>
</div>
<?php
$js = <<< 'SCRIPT'
$(document).ready(
    function(){
        $('.js--send-now-button').on('click', function () {
            var _this = $(this);
            _this.closest('div').find('.js--send-now-input').prop('disabled', false);
            _this.closest('form').submit();
        });
        $("#message-calc_messwhomtype").change(
            function() {
                var key = $("#message-calc_messwhomtype option:selected").val();
    	        $.ajax({
                    type:"POST",
                    url:"/message/ajaxgroup",
                    data: "type="+key,
                    success: function(users) {
                        $(".field-message-refinement_id").html(users);
                    }
                });
            }
        );
    }
);
SCRIPT;
$this->registerJs($js);