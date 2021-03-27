<?php

/**
 * @var View $this
 * @var array $languages
 * @var array $urlParams
 */

use common\components\helpers\IconHelper;
use school\models\Auth;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Languages');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
$this->params['breadcrumbs'][] = Yii::t('app','Languages');
/** @var Auth $auth */
$auth = \Yii::$app->user->identity;
$this->params['sidebar'] = [
    'viewFile' => '//translate/sidebars/_languages',
    'params' => ['urlParams' => $urlParams, 'canCreate' => in_array($auth->roleId, [3, 9])],
];
?>
<table class="table table-stripped table-hover table-condensed table-bordered small">
    <thead>
        <tr>
            <th>№</th>
            <th><?= Yii::t('app','Name') ?></th>
            <?php if (in_array($auth->roleId, [3, 9])) { ?>
                <th><?= Yii::t('app','Act.') ?></th>
            <?php } ?>
    </thead>
    <tbody>
        <?php foreach($languages as $key => $lang) { ?>
            <tr>
                <td class="tbl-cell-5"><?= $key + 1 ?></td>
                <td><?= $lang->name ?></td>
                <?php if (in_array($auth->roleId, [3, 9])) { ?>
                    <td class="tbl-cell-5 text-center">
                        <?= Html::a(IconHelper::icon('pencil'),['translationlang/update', 'id'=>$lang->id],['title'=>Yii::t('app','Edit')]) ?>
                        <?= Html::a(IconHelper::icon('trash'),['translationlang/delete', 'id'=>$lang->id],['title'=>Yii::t('app','Delete')]) ?>
                    </td>
                <?php } ?>
            </tr>
            <?php } ?>
    </tbody>
</table>