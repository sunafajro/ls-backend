<?php

/**
 * @var View  $this
 * @var array $statuses
 * @var array $urlParams
 * @var array $users
 */

use common\components\helpers\IconHelper;
use common\widgets\alert\AlertWidget;
use school\widgets\userInfo\UserInfoWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Users');
$this->params['breadcrumbs'][] = Yii::t('app','Users');
?>
<div class="row user-index">
    <div id="sidebar" class="col-xs-12 col-sm-12 col-md-2 col-lg-2 col-xl-2">
        <?= UserInfoWidget::widget() ?>
        <h4><?= Yii::t('app', 'Actions') ?>:</h4>
        <div class="form-group">
            <?= Html::a(
                    IconHelper::icon('plus') . ' ' . Yii::t('app', 'Add'),
                    ['user/create'],
                    ['class' => 'btn btn-success btn-sm btn-block']
            ) ?>
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
                <option value="1"<?= ($urlParams['active'] == '1') ? ' selected' : '' ?>><?= Yii::t('app', 'Enabled') ?></option>
                <option value="0"<?= ($urlParams['active'] == '0') ? ' selected' : '' ?>><?= Yii::t('app', 'Disabled') ?></option>
            </select>
        </div>
        <div class="form-group">
            <select name="role" class="form-control input-sm">
                <option value="all"><?= Yii::t('app', '-all roles-') ?></option>
                <?php foreach($statuses as $roleKey => $roleName) : ?>
                <option value="<?= $roleKey ?>"<?= ($urlParams['role'] == $roleKey) ? ' selected' : '' ?>><?= $roleName ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <?= Html::submitButton(
                    IconHelper::icon('filter') . ' ' . Yii::t('app', 'Apply'),
                    ['class' => 'btn btn-info btn-sm btn-block']
            ) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div id="content" class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?= AlertWidget::widget() ?>
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
            <?php foreach($users as $u) { ?>
                <tr>
                    <td><?= Html::a($u['name'], ['user/view', 'id' => $u['id']]) ?></td>
                    <td><?= $u['login'] ?></td>
                    <td><?= $u['role'] ?></td>
                    <td><?= $u['office'] ?></td>
                    <td>
                        <?php if ((int)$u['visible'] === 1) { ?>
                            <?= Html::a(
                                    IconHelper::icon('times'),
                                    ['user/disable', 'id' => $u['id']],
                                    ['Title' => Yii::t('app', 'Disable user')]
                            ) ?>
                            <?= Html::a(IconHelper::icon('pencil'), ['user/update', 'id' => $u['id']], ['title' => Yii::t('app', 'Update user')]) ?>
                            <?= Html::a(IconHelper::icon('picture-o'), ['user/upload', 'id' => $u['id']], ['title' => Yii::t('app', 'Add picture')]) ?>
                        <?php } else { ?>
                            <?= Html::a(IconHelper::icon('check'), ['user/enable', 'id' => $u['id']], ['Title' => Yii::t('app', 'Enable user')]) ?>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
