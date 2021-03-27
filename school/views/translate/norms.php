<?php

/**
 * @var View $this
 * @var array $norms
 * @var array $urlParams
 */

use common\components\helpers\IconHelper;
use school\models\Auth;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Translation pay norms');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
$this->params['breadcrumbs'][] = Yii::t('app','Translation pay norms');
/** @var Auth $auth */
$auth = \Yii::$app->user->identity;
$this->params['sidebar'] = $this->render('sidebars/_norms', ['urlParams' => $urlParams, 'canCreate' => in_array($auth->roleId, [3, 9])]);
?>
<table class="table table-stripped table-hover table-condensed table-bordered small">
    <thead>
        <tr>
            <th>№</th>
            <th><?= Yii::t('app','Name') ?></th>
            <th class="tbl-cell-10 text-center"><?= Yii::t('app','Type') ?></th>
            <th class="tbl-cell-10 text-center"><?= Yii::t('app','Value') ?></th>
            <?php if (in_array($auth->roleId, [3, 9])) { ?>
                <th class="tbl-cell-5 text-center"><?= Yii::t('app','Act.') ?></th>
            <?php } ?>
    </thead>
    <tbody>
        <?php foreach($norms as $key => $norm) { ?>
            <tr>
                <td class="tbl-cell-5"><?= $key + 1 ?></td>
                <td><?= $norm->name ?></td>
                <td class="tbl-cell-10 text-center">
                <?php switch($norm->type) {
                    case 1: echo Yii::t('app','Written'); break;
                    case 2: echo Yii::t('app','Oral'); break;
                    case 3: echo Yii::t('app','Other'); break;
                } ?>
                </td>
                <td class="tbl-cell-10 text-center"><?= $norm->value ?></td>
                <?php if (in_array($auth->roleId, [3, 9])) { ?>
                    <td class="tbl-cell-5 text-center">
                        <?= Html::a(IconHelper::icon('pencil'),['translationnorm/update', 'id'=>$norm->id],['title'=>Yii::t('app','Edit')]) ?>
                        <?= Html::a(IconHelper::icon('trash'),['translationnorm/delete', 'id'=>$norm['id']],['title'=>Yii::t('app','Delete')]) ?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </tbody>
</table>