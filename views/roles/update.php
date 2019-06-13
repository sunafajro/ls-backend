<?php

/**
 * @var yii\web\View    $this
 * @var app\models\Role $model
 * @var string          $userInfoBlock
 */

use yii\widgets\Breadcrumbs;
$this->title = 'Система учета :: ' . Yii::t('app', 'Update role');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Roles'), 'url' => ['admin/roles']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="row row-offcanvas row-offcanvas-left role-update">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
        <?= $userInfoBlock ?>
    </div>
    <div id="content" class="col-sm-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
        ]); ?>
        <?php endif; ?>
        <p class="pull-left visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>
        <?php if(Yii::$app->session->hasFlash('error')) : ?>
        <div class="alert alert-danger" role="alert">
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
        <?php endif; ?>
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>