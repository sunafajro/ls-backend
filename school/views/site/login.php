<?php
/**
 * @var View       $this
 * @var LoginForm  $model
 * @var ActiveForm $form
 */

use school\models\forms\LoginForm;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\View;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Login');
$this->params['breadcrumbs'][] = Yii::t('app','Login');
?>
<div class="site-login">
    <div class="col-sm-12">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <?php
                echo Html::img('@web/images/flowers/'.rand(0,100).'.jpg', ['alt'=>'Цветочки', 'class'=>'thumbnail', 'style' => 'margin: 0 auto']); 
            ?>
        </div>
        <div class="col-sm-2"></div>
    </div>
    <div class="col-sm-12">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <p class="text-center"><?php echo Yii::t('app','Please fill out the following fields to login:'); ?></p>
            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'options' => ['class' => 'form-horizontal'],
                'fieldConfig' => [
                    'template' => "{label}\n<div class=\"col-sm-4\">{input}</div>\n<div class=\"col-sm-4\">{error}</div>",
                    'labelOptions' => ['class' => 'col-sm-4 control-label'],
                ],
            ]); ?>
            <?= $form->field($model, 'username')->textInput() ?>
            <?= $form->field($model, 'password')->passwordInput() ?>
            <div class="form-group">
                <div class="text-center">
                    <?= Html::submitButton(Yii::t('app','Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-sm-2"></div>
    </div>
</div>
