<?php
use moonland\tinymce\TinyMCE;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="calc-message-form">
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
    <?=$form->field($model, 'calc_messwhomtype')->dropDownList($item = $types, ['prompt' => Yii::t('app', '-select-'), 'disabled' => $model->isNewRecord ? false : true])?>
    <?=$form->field($model, 'refinement')->hiddenInput(['value' => 0])->label(false)?>
    <?=$form->field($model, 'refinement_id')->hiddenInput(['value' => 0])->label(false)?>
    <div class="form-group">
        <?=Html::submitButton($model->isNewRecord ? Yii::t('app', 'Add') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>
    <?php ActiveForm::end();?>

</div>

<?php
$this->registerJs('$(document).ready(
function(){
    $("#message-calc_messwhomtype").change(
        function(){
            var key = $("#message-calc_messwhomtype option:selected").val();
    	    $.ajax({type:"POST", url:"/message/ajaxgroup", data: "type="+key, success: function(users){
            $(".field-message-refinement_id").html(users);}});
});});');
?>

