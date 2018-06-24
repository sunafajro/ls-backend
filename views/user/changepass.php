<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */

$this->title = \Yii::t('app','Change password').': '.$model->name;
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app','Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = \Yii::t('app','Change password');

?>
<div class="user-changepass">
        <?php 
            if(Yii::$app->session->getFlash('success')){
                echo "<div class='alert alert-success' role='alert'>";
                echo Yii::$app->session->getFlash('success');
                echo "</div>";
                }
            if(Yii::$app->session->getFlash('error')){
                echo "<div class='alert alert-danger' role='alert'>";
                echo Yii::$app->session->getFlash('error');
                echo "</div>";
                }
            ?>
    <div class="changepass-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'pass')->passwordInput() ?>

        <?= $form->field($model, 'pass_repeat')->passwordInput()->label(\Yii::t('app','Password repeat')) ?>
 
        <div class="form-group">
            <?= Html::submitButton(\Yii::t('app','Update'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>