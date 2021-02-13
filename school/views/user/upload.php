<?php

/**
 * @var View       $this
 * @var User       $user
 * @var UploadForm $model
 */

use school\models\forms\UploadForm;
use school\models\User;
use common\widgets\alert\AlertWidget;
use school\widgets\userInfo\UserInfoWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Add image');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Users'), 'url' => ['user/index']];
$this->params['breadcrumbs'][] = ['label' => $user->name, 'url' => ['user/index', 'id' => $user->id]];
$this->params['breadcrumbs'][] = Yii::t('app','Upload image');
?>
<div class="row user-upload">
    <div id="sidebar" class="col-xs-12 col-sm-12 col-md-2 col-lg-2 col-xl-2">
        <?= UserInfoWidget::widget() ?>
        <ul>
			<li>У пользователя может быть только одно фото. Новое загруженное фото заменяет старое.</li>
		</ul>
    </div>
    <div id="content" class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?= AlertWidget::widget() ?>
        <?php
            $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
        ?>
        <?= $form->field($model, 'file')->fileInput()->label(Yii::t('app','Image')) ?>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app','Upload'), ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
        <p>Текущая фотография:</p>
        <?php if ($user->logo != '' && $user->logo != '0') { ?>
            <?= Html::img('@web/uploads/user/' . $user->id . '/logo/' . $user->logo, ['alt' => 'foto', 'class' => 'img-thumbnail']) ?>
        <?php } else { ?>
            <p class='text-danger'>У данного пользователя отсутствует фото!</p>
        <?php } ?>
    </div>
</div>
