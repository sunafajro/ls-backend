<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
/* @var $this yii\web\View */
/* @var $model app\models\CalcTeacher */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="calc-teacher-form">

    <?php $form = ActiveForm::begin(); ?>
    <?php if((int)Yii::$app->session->get('user.ustatus') === 3 || (int)Yii::$app->session->get('user.uid') === 296) { ?>
        <?= $form->field($model, 'name')->textInput() ?>

        <?php
            echo $form->field($model, 'birthdate')->widget(DateTimePicker::className(), [
                        'pluginOptions' => [
                            'language' => 'ru',
                                'format' => 'yyyy-mm-dd',
                                'todayHighlight' => true,
                                'minView' => 2,
                                'maxView' => 4,
                                'weekStart' => 1,
                                'autoclose' => true,
                        ]
                 ]);
        ?>


        <?= $form->field($model, 'phone')->textInput() ?>

        <?= $form->field($model, 'email')->textInput() ?>

        <?= $form->field($model, 'social_link')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

        <?php
            // корпоративная надбавка при создании нового преподавателя 0
            if($model->isNewRecord){ 
                echo $form->field($model, 'value_corp')->textInput(['value'=>0]);
            } else {
                echo $form->field($model, 'value_corp')->textInput();
            } ?>

    
    <?= $form->field($model, 'calc_statusjob')->dropDownList($items=$statusjobs,['prompt'=>Yii::t('app', '-select-')]) ?>

    <?php } ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

    <?php
        // параметр с нами/не с нами, при создании новго преподавателя по умолчанию 0 
        if($model->isNewRecord){
            echo $form->field($model, 'old')->hiddenInput(['value'=>'0'])->label(false);
        } else {
            echo $form->field($model, 'old')->dropDownList($items=['0'=>Yii::t('app','With us'),'1'=>Yii::t('app','Not with us'), '2'=>'В отпуске', '3'=>'В декрете']);
        } ?>

    <?php
	    if($model->isNewRecord){ 
          echo $form->field($model, 'visible')->hiddenInput(['value'=>'1'])->label(false);
	    } 
    ?>

    <div class="form-group">
        <?php
        if ($model->isNewRecord) {
            echo Html::submitButton(Yii::t('app', 'Add new'), ['class' => 'btn btn-success', 'name' => 'new']);
            if ((int)Yii::$app->session->get('user.uid') !== 296) {
              echo ' ';
              echo Html::submitButton(Yii::t('app', 'Add existent'), ['class' => 'btn btn-info', 'name' => 'exist']);
            }
        } else {
            echo Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary']);
        } 
        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php $this->registerJs('
$(document).ready(
  function(){
    $("#teacher-address").suggestions({
        token: "9ac43b0c02b76d2f8be18c637ce94133d7c66e7f",
        type: "ADDRESS",
        count: 5,
        onSelect: function(suggestion) {
          console.log(suggestion);
        }
      });
  });
');
?>
