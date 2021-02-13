<?php

/**
 * @var View       $this
 * @var ActiveForm $form
 * @var LoginForm  $model
 */

use client\models\LoginForm;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\View;

$this->title = Yii::$app->params['siteTitle'];
$this->params['breadcrumbs'][] = Yii::t('app', 'Login');
$error = $model->hasErrors() ? $model->getErrors() : [];
$columnStyle = 'col-xs-12 col-sm-10 col-md-8 col-lg-8 col-sm-offset-1 col-md-offset-2 col-lg-offset-2';
?>
<div class="site-login">
    <div class="row" style="margin-bottom: 1rem">
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 col-sm-offset-1 col-md-offset-2 col-lg-offset-2">
            <a href="https://language-school.ru" target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" x="0" y="0" viewBox="0 0 407.5 300.3" xml:space="preserve" class="site-logo">
				    <path class="line" d="M3.5 299.9l371.3-126.4c2.2-.8 4.1-2.4 5-4.5l27.1-59.3c1.1-2.3-1.4-4.7-3.7-3.6L1.7 295.3c-2.8 1.4-1.1 5.6 1.8 4.6z" fill="#ff0000"/>
                    <path class="desc" d="M310.3 240.6v13.8h-8.4v-6.5h-20.8v6.5h-8.4v-13.8h1.2c1.9 0 3.3-1.3 4.1-3.9.8-2.6 1.3-6.3 1.5-11.2l.4-10H306v25.2h4.3zm-23.7-5.5c-.4 2.4-1.1 4.3-2 5.6h12.3v-17.9h-9.3l-.1 3.5c-.2 3.4-.5 6.4-.9 8.8zm37-19.6h12.3l10.7 32.4h-9l-7.9-23.5-7.6 23.5H313l10.6-32.4zm45.5 52.1h-12.3L346 300.1h9.1l2-6.3h11.4l2.1 6.3h9l-10.5-32.5zm-9.7 19.4l3.4-10.5 3.5 10.5h-6.9zm19.7-71.5v32.4h-9v-8.2h-5l-5.3 8.2h-9.6l6.6-9.5c-2.2-1-3.8-2.3-5-4.1-1.1-1.8-1.7-4-1.7-6.5s.6-4.7 1.8-6.6c1.2-1.8 2.9-3.3 5.1-4.3 2.2-1 4.8-1.5 7.7-1.5h14.4zm-19.9 12.2c0 1.6.5 2.9 1.4 3.7.9.9 2.2 1.3 4 1.3h5.5v-10h-5.4c-3.7 0-5.5 1.7-5.5 5zM188.9 291l-12.6-23.4h9.7l8 14.7 7.4-14.7h8.9l-17 32.4h-8.9l4.5-9zm29.7 7c-2.6-1.4-4.6-3.3-6.1-5.8s-2.2-5.2-2.2-8.4c0-3.1.7-5.9 2.2-8.4 1.5-2.5 3.5-4.4 6.1-5.8 2.6-1.4 5.5-2.1 8.8-2.1 2.8 0 5.4.5 7.7 1.5s4.2 2.5 5.7 4.4l-5.6 5.1c-2-2.4-4.5-3.6-7.3-3.6-1.7 0-3.2.4-4.5 1.1-1.3.7-2.3 1.8-3.1 3.1-.7 1.3-1.1 2.9-1.1 4.6 0 1.7.4 3.3 1.1 4.6.7 1.3 1.7 2.4 3.1 3.1 1.3.7 2.8 1.1 4.5 1.1 2.9 0 5.3-1.2 7.3-3.6l5.6 5.1c-1.5 1.9-3.4 3.3-5.7 4.4-2.3 1-4.8 1.5-7.7 1.5-3.3.2-6.2-.5-8.8-1.9zm57.5-30.4V300H267v-25.2h-12.3V300h-9v-32.4h30.4zm33 25.4v7.1h-26v-32.4h25.4v7.1h-16.4v5.5h14.4v6.9h-14.4v5.9h17zm13-25.4l5.8 8.8 5.7-8.8h10.7l-10.7 15.5 11.6 17h-10.8l-6.5-9.6-6.5 9.6h-10.6l11.5-16.5-10.8-15.9h10.6z"/>
                    <path class="title" d="M92.2 13.8c8-3.6 14.2-3.9 18.5-.9 4.4 3 7 7.9 8 14.6 3.3-12.6 4.8-16.9 7.4-21.9 2.5-4.6 5.5-5.8 8.3-2.7 6.2 6.7 9.8 16.2 4.3 38.7-4 15.8-8.7 34.1-14.1 54.8-5.4 20.6-9.1 34.9-11 42.8-1.9 7.9-7.2 37.2-9.7 50.4-2.5 13.1-4.7 27.6-6.8 43.5-.3 3.3-2 4.3-4.9 3.2-7.8-2.8-10.3-11.4-9.9-25.6.3-7.3 4.6-42.4 9.2-67.7-8.2 6.8-16.5 12.1-24.8 15.8-13.4 6-24.4 6.5-33 1.7-8.6-4.9-14.3-12.7-17.2-23.5-3.9-15.1-2.3-31.3 4.8-48.6 7.2-17.4 17.2-32.5 30-45.5 13.2-13 26.7-22.7 40.9-29.1zM42.1 213.1c-9.1 14.3-21.6 43-28.7 59.4-1 2.4-2.1 3.8-3.3 3.9-1.2.2-2.7-.9-4.3-3.4-1.6-2.4-2.5-5.7-2.5-9.7 0-11.2 6.3-29.8 16.3-48.1s19.6-33.5 28.8-45.5 17.2-21.7 23.8-29c2.9-3 5.8-5.6 8.7-7.6l19.7.2c-41 48.6-49.4 65.4-58.5 79.8zM99 30.5c-14.7 6.6-30.2 22-46.6 46.1-16.4 24-22.5 43.9-18.3 59.8 1.4 5.3 4.3 8.7 8.9 10.3 4.6 1.6 9.5 1.2 14.8-1.2 7.7-3.4 14.2-7.6 19.7-12.7s12.1-12.5 19.8-22.3c0 0 7-23.4 9.3-31.4 10.4-36.6 5.3-54.4-7.6-48.6zm155.7 87.9c2.6 10.9 1 21.9-4.7 33.2-5.7 11.2-14.8 19.7-27.4 25.5-10.6 4.9-18.6 4.9-23.8 0-5.2-4.9-7.2-13.2-6.1-25.1.5-8.2 2.9-20.4 7.4-36.5 4.5-16.2 7.5-27.9 8.9-35.1.1-.9.6-1.9 1.3-3 .8-1.1 1.6-1.6 2.6-1.7 1.4-.1 3 .7 5 2.5 2 1.7 3.5 3.7 4.6 5.9 2.4 4.9 2 14-1.4 27.4.9-.8 2.6-1.9 4.9-3.3 5.1-2.7 10-3.2 14.6-1.5.9.3 2.2.8 4.1 1.4 1.9.6 3.3 1.2 4.2 1.6.9.5 1.9 1.4 3.1 2.9 1.3 1.2 2.1 3.3 2.7 5.8zm-32.7 4.4c-2.2 2-3.7 4.1-5.2 5.9-4.7 18.5-6.1 30.7-4.4 36.7 2 6.8 5.7 8.9 11.2 6.2 4.8-2.3 9.1-7.6 12.8-16.1 3.8-8.4 5-17.6 3.8-27.5-1-7.5-1.7-11.6-2.1-12.3-.8-1.3-1.6-1.9-2.6-2-1-.1-2.8.7-3.8 1.3-1.1.6-4.2 2.7-5.2 3.6-1.4 1.4-3 2.8-4.5 4.2zm63.5 24.8c-9.3 4.4-16.4 4-21.2-1.2-4.8-5.2-6.6-13.5-5.4-24.8.8-9.4 3-21.6 6.5-36.6s6.9-27.2 10.2-36.5c1.4-3.9 3.4-5.4 5.8-4.4 2.8 1.2 5 3.9 6.4 8.3 1.4 4.4 1.6 10.3.5 17.8-.7 3.2-2.4 10.7-5.3 22.6-2.9 11.9-4.7 20.5-5.5 25.8-1.1 8.3-1 14.4.5 18.3 1.5 3.9 4.2 4.8 8.2 2.7 5.4-2.7 10.3-8.8 14.9-18.2 4.5-9.4 7.7-19.3 9.4-29.8.7-3 2.1-3.7 4.1-2 3 2.6-1.8 10.3-4.1 17.7-2.5 7.8-1.6 5.3-5.6 14.9-3.9 9.3-12.2 21.9-19.4 25.4z"/>
                    <path class="title" d="M250.2 150.8l11-19.2.6-8.1-9.6 17.6zm83-128c4.9 5.7 5 12.7 2.8 21.7-1.1 4.6-4.1 11.8-6.7 19.8-2.5 8-3.8 17.1-6.4 27.6-2.7 10.5-4.1 23.2-6.9 39-.1 1.1-.6 2-1.6 2.6-1 .6-2 .6-3.2 0-6-2.8-7.6-12-6.4-26.8 1-13.9 3.3-25.8 5.9-36.7 2.7-10.9 7.5-25.8 14.8-44.8 2-5.2 4.8-5.8 7.7-2.4zm17.5 89.2c-6.7-9.7-9.3-13-14.6-18.7-5.2-5.5-9.6-6.5-15.8-13.2-1.5-1.6-.6-3.7-.1-4.7.4-1 1.8-2.6 4.2-4.4 20.3-15.5 30-26.5 44.4-64.7 2.1-5.6 6.3-7.7 8.1-4.4 2.7 4.9 3.3 10 1.4 16.6-3.3 10.8-10.8 25.7-18.7 36-6.7 8.9-20 22.5-20 22.5 6.4 7 11.4 11.1 14.6 16.4 3.5 5.9 4.1 9.3 4.3 12.9.2 2.3-3 12.7-7.8 5.7zm-147.5 62.7l-1.5-3.9c-3 2.6-16.4 13.3-25.5 19.8v-.1c-2.1-3.8-4.2-5.2-7-6.6-4.8-2.4-10.4-3.8-17.6-.5 17.4-19.9 27.7-33 32.2-47.1 5.3-16.4 3.2-28.1-6.2-35-4.2-3.2-8.6-4.6-13-4.2-4.4.4-9.8 2.4-16.2 5.9-8.5 4.6-13.9 10.6-17.5 15.1-2.2 2.7-4.3 8.8-4.7 11.2-.7 3.9-1.1 9.7.7 11.5 3.2 3.2 1-1.1 16.3-16.1 13.5-13.3 22.2-19.1 27-16.1 3.2 1.9 4 6.4 2.3 13.6-1.7 7.2-5 16.4-12.3 27.9-7.2 11.4-17.6 23.4-29 35.6-10.1 10.8-12.2 20.3-6.4 28.7 1.1 1.4 2.7.9 4.7-1.6 5.6-6.9 11.9-12.3 18.9-16.4 7-4 11.9-5.2 14.3-3.6.2.2 2.5 1.1 3.7 4.2-4 2.4-8.7 6.2-12.7 9.2-10.9 8-16.5 13.1-23.3 19.5-4.7 4.4-10.2 10.7-13.4 14.2-5.1 5.7-10.7 12.2-17.3 24.7-4.4 8.4-7.4 20.2-4.1 27.9 2.7 6.3 5.1 7.9 12.2 7.6 9.5-.5 19.6-7 28.7-14.4 8.5-7 19.9-21.2 24.1-28.1 8.3-13.4 12.1-21.9 13.2-25 4.4-10.7 8.1-23.8 4.8-37.6 7-6.3 21.6-17.8 24.6-20.3zm-35 41.4c-1.2 5.1-6.9 14.4-9.8 19.3-1.4 2.2-4.4 6.8-11.2 16.4-4.8 6.8-12.4 15.6-14.2 17.2-13.2 12.1-23.9 17.5-28.1 14.2-2.8-2.2-1-11.4 1.2-17 .6-1.4 1.3-2.7 2.1-4.2 4.9-9.2 13.6-19.9 19.8-26.2 6-6.1 17.4-16.9 27.7-24.7 4-3 9.2-7.2 12.4-9.1.9 2.7 1.3 9.2.1 14.1z"/>
			    </svg>
            </a>
        </div>
        <div class="col-xs-10 col-sm-8 col-md-6 col-lg-6">
            <h1>Личный кабинет клиента</h1>
        </div>
    </div>
    <?php if (isset($error['date'])) { ?>
        <div class="row" style="margin-bottom: 1rem">
            <div class="<?= $columnStyle ?>">
                <?= Html::tag('div', $error['date'][0] ?? Yii::t('app', 'An error occurs.'), ['class' => 'alert alert-danger', 'style' => 'margin-bottom: 0px']) ?>
            </div>
        </div>
    <?php } ?>
    <div class="row" style="margin-bottom: 1rem">
        <div class="<?= $columnStyle ?>">
            <p style="margin-bottom: 0px"><?= Yii::t('app', 'Please fill out the following fields to login') ?>:</p>
        </div>
    </div>
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
    ]); ?>
        <div class="row">
            <div class="<?= $columnStyle ?>">
                <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="<?= $columnStyle ?>">
                <?= $form->field($model, 'password')->passwordInput() ?>
            </div>
        </div>
        <div class="row">
            <div class="<?= $columnStyle ?> text-center">
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>
