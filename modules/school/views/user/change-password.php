<?php

/**
 * @var View $this
 * @var User $model
 */

use app\modules\school\models\User;
use app\widgets\alert\AlertWidget;
use app\widgets\userInfo\UserInfoWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Change password') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app','Change password');
?>
<div class="row user-change-password">
    <div id="sidebar" class="col-xs-12 col-sm-12 col-md-2 col-lg-2 col-xl-2">
        <?= UserInfoWidget::widget() ?>
        <ul>
			<li>Укажите и сохраните новый пароль</li>
		</ul>
    </div>
    <div id="content" class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?= AlertWidget::widget() ?>
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'pass')->passwordInput() ?>
        <?= $form->field($model, 'pass_repeat')->passwordInput()->label(\Yii::t('app','Password repeat')) ?>
         <div class="form-group">
            <?= Html::submitButton(\Yii::t('app','Update'), ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>