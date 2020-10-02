<?php

/**
 * @var View       $this
 * @var User       $model
 * @var ActiveForm $form
 * @var array      $cities
 * @var array      $offices
 * @var array      $statuses
 * @var array      $teachers
 */

use app\modules\school\assets\UsersFormAsset;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

UsersFormAsset::register($this);
?>
<div class="user-form">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <?= $form->field($model, 'name')->textInput() ?>
    <?= $form->field($model, 'status')->dropDownList($statuses,['prompt' => Yii::t('app','-select-')]) ?>
    <?php
        if(!$model->isNewRecord && $model->status == 4){
            echo $form->field($model, 'calc_office')->dropDownList($offices,['prompt' => Yii::t('app','-select-')]);
            echo $form->field($model, 'calc_city')->dropDownList($cities,['prompt' => Yii::t('app','-select-')]);
        } else {
            echo $form->field($model, 'calc_office')->dropDownList($offices,['prompt' => Yii::t('app','-select-'), 'disabled' => true]);
            echo $form->field($model, 'calc_city')->dropDownList($cities,['prompt' => Yii::t('app','-select-'), 'disabled' => true]);
        }
    ?>
    <?= $form->field($model, 'calc_teacher')->dropDownList($teachers,['prompt' => Yii::t('app','-select-')]) ?>
    <?= $form->field($model, 'login')->textInput() ?>
    <?php
        if($model->isNewRecord){
            echo $form->field($model, 'pass')->passwordInput();
        }
    ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app','Add') : Yii::t('app','Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
