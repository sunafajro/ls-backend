<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Система учета :: ' . Yii::t('app', 'Add image') . ': ' . $user->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app','Upload image');
?>

<div class="row row-offcanvas row-offcanvas-left user-upload">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= $userInfoBlock ?>
        <ul>
			<li>У пользователя может быть только одно фото. Новое загруженное фото заменяет старое.</li>
		</ul>
    </div>
    <div id="content" class="col-sm-10">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
        <?php
            $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
        ?>
        <?= $form->field($model, 'file')->fileInput()->label(Yii::t('app','Image')) ?>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app','Upload'), ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
        <p>Текущая фотография:</p>
        <?php if ($user->logo != '' && $user->logo != '0') : ?>
            <img src='uploads/user/<?= $user->id ?>/logo/<?= $user->logo ?>' alt='foto' class='img-thumbnail'>
        <?php else : ?>
            <p class='text-danger'>У данного пользователя отсутствует фото!</p>
        <?php endif; ?>
    </div>
</div>
