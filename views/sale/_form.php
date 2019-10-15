<?php

use app\model\Sale;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;

/**
 * @var View  $this
 * @var Sale  $model
 * @var array $types
 */
?>
<div class="discount-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'name')->textInput() ?>
    <?php if ($model->isNewRecord) { ?>
        <?= $form->field($model, 'procent')->dropDownList($types, ['prompt' => Yii::t('app', '-select-')]) ?>
        <?= $form->field($model, 'value')->textInput() ?>
        <div id="sale-base-block" style="display: none">
            <?= $form->field($model, 'base')->textInput() ?>
        </div>
    <?php } ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php 
$js = <<< 'SCRIPT'
$(document).ready(function() {
  $("#sale-procent").change(function(e) {
    if(e.target.value === "2") {
      $("#sale-base-block").show();
    } else {
      $("#sale-base-block").hide();
      $("#sale-base").val('');
    }
  });
});
SCRIPT;
$this->registerJs($js);