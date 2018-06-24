<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Studnorm */

$this->title = 'Система учета :: ' . Yii::t('app', 'Update student norm') . ' / ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'References'), 'url' => ['reference/phonebook']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Student norms'), 'url' => ['reference/studpaynorm']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="row row-offcanvas row-offcanvas-left studpaynorm-update">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <div class="well well-sm small">
            <span class="font-weight-bold"><?= Yii::$app->session->get('user.uname') ?></span>
            <?php if(Yii::$app->session->get('user.uteacher')) { ?>
                <?= Html::a('', ['teacher/view', 'id'=>Yii::$app->session->get('user.uteacher')], ['class'=>'fa fa-user btn btn-default btn-xs']); ?>                   
            <?php } ?>               
            <br />
            <?= Yii::$app->session->get('user.stname') ?>
            <?php if(Yii::$app->session->get('user.ustatus')==4) { ?>
                <br />
                <?= Yii::$app->session->get('user.uoffice') ?>
            <?php } ?>
            <br><span id="timer" class="text-danger">20:00</span>
        </div>
    </div>
    <div id="content" class="col-sm-6">
        <p class="pull-left visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>

        <?php if(Yii::$app->session->hasFlash('error')) { ?>
        <div class="alert alert-danger" role="alert">
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
        <?php } ?>
   
        <?php if(Yii::$app->session->hasFlash('success')) { ?>
        <div class="alert alert-success" role="alert">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
        <?php } ?>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
	</div>
</div>