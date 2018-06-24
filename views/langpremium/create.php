<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CalcPhonebook */

$this->title = 'Система учета :: ' . Yii::t('app','Create phone');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'References'), 'url' => ['reference/phonebook']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Language premiums'), 'url' => ['reference/langpremium']];
$this->params['breadcrumbs'][] = Yii::t('app','Add');
?>
<div class="row row-offcanvas row-offcanvas-left phonebook-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= $userInfoBlock ?>
    </div>
    <div id="content" class="col-sm-6">
        <p class="pull-left visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>

        <?php if (Yii::$app->session->hasFlash('error')) : ?>
        <div class="alert alert-danger" role="alert">
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
        <?php endif; ?>
   
        <?php if (Yii::$app->session->hasFlash('success')) : ?>
        <div class="alert alert-success" role="alert">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
        <?php endif; ?>
        <?= $this->render('_form', [
            'model' => $model,
            'languages' => $languages
        ]) ?>
	</div>
</div>
