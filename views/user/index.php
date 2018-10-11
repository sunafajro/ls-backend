<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $userlist */
$this->title = 'Система учета :: '.Yii::t('app','Users');
$this->params['breadcrumbs'][] = Yii::t('app','Users');
?>

<div class="row row-offcanvas row-offcanvas-left user-index">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= $userInfoBlock ?>
        <h4><?= Yii::t('app', 'Actions') ?>:</h4>
        <div class="form-group">
            <?= Html::a('<span class="fa fa-plus" aria-hidden="true"></span> ' . Yii::t('app', 'Add'), ['user/create'], ['class' => 'btn btn-success btn-sm btn-block']) ?>
        </div>
        <h4><?= Yii::t('app', 'Filters') ?>:</h4>
        <?php
            $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['user/index'],
            ]);
        ?>
        <div class="form-group">
            <select name="active" class="form-control input-sm">
                <option value="all"><?= Yii::t('app', '-all states-') ?></option>
                <option value="1"<?= ($url_params['active'] == '1') ? ' selected' : '' ?>><?= Yii::t('app', 'Enabled') ?></option>
                <option value="0"<?= ($url_params['active'] == '0') ? ' selected' : '' ?>><?= Yii::t('app', 'Disabled') ?></option>
            </select>
        </div>
        <div class="form-group">
            <select name="role" class="form-control input-sm">
                <option value="all"><?= Yii::t('app', '-all roles-') ?></option>
                <?php foreach($statuses as $s) : ?>
                <option value="<?= $s['id'] ?>"<?= ($url_params['role'] == $s['id']) ? ' selected' : '' ?>><?= $s['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <?= Html::submitButton('<span class="fa fa-filter" aria-hidden="true"></span> ' . Yii::t('app', 'Apply'), ['class' => 'btn btn-info btn-sm btn-block']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div id="content" class="col-sm-10">
        <p class="pull-left visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>

        <?php if(Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger" role="alert">
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
        <?php endif; ?>
   
        <?php if(Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success" role="alert">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
        <?php endif; ?>

        <table class="table table-hover table-stripped table-bordered table-condensed small">
            <thead>
                <tr>
                    <th>Имя</th>
                    <th>Логин</th>
                    <th>Роль</th>
                    <th>Офис</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($users as $u) : ?>
                <tr>
                    <td><?= $u['name'] ?></td>
                    <td><?= $u['login'] ?></td>
                    <td><?= $u['role'] ?></td>
                    <td><?= $u['office'] ?></td>
                    <td>
                        <?= ($u['visible']==1) ? Html::a('<span class="fa fa-times"></span>', ['user/disable', 'id' => $u['id']], ['Title' => Yii::t('app', 'Disable user')]) : Html::a('<span class="fa fa-check"></span>', ['user/enable', 'id' => $u['id']], ['Title' => Yii::t('app', 'Enable user')]) ?>
                        <?= ($u['visible']==1) ? Html::a('<span class="fa fa-pencil" aria-hidden="true"></span>', ['user/update', 'id' => $u['id']], ['title' => Yii::t('app', 'Update user')]) : '' ?>
                        <?= ($u['visible']==1) ? Html::a('<span class="fa fa-key" aria-hidden="true"></span>', ['user/changepass', 'id' => $u['id']], ['title' => Yii::t('app', 'Change password')]) : '' ?>
                        <?= ($u['visible']==1) ? Html::a('<span class="fa fa-picture-o" aria-hidden="true"></span>', ['user/upload', 'id' => $u['id']], ['title' => Yii::t('app', 'Add picture')]) : '' ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
