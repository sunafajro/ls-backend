<?php

/**
 * @var View $this
 * @var array $norms
 * @var array $urlParams
 */

use common\components\helpers\IconHelper;
use school\models\AccessRule;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Translation pay norms');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
$this->params['breadcrumbs'][] = Yii::t('app','Translation pay norms');
$this->params['sidebar'] = ['urlParams' => $urlParams];

$canUpdate = AccessRule::checkAccess('translationnorm_update');
$canDelete = AccessRule::checkAccess('translationnorm_delete');
?>
<table class="table table-stripped table-hover table-condensed table-bordered small">
    <thead>
        <tr>
            <th>№</th>
            <th><?= Yii::t('app','Name') ?></th>
            <th class="tbl-cell-10 text-center"><?= Yii::t('app','Type') ?></th>
            <th class="tbl-cell-10 text-center"><?= Yii::t('app','Value') ?></th>
            <th class="tbl-cell-5 text-center"><?= Yii::t('app','Act.') ?></th>
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
                <td class="tbl-cell-5 text-center">
                    <?php
                        if ($canUpdate) {
                            echo Html::a(
                                    IconHelper::icon('pencil'),
                                    ['translationnorm/update', 'id'=>$norm->id],
                                    ['title'=>Yii::t('app','Edit'), 'style' => 'margin-right: 2px']
                            );
                        }
                        if ($canDelete) {
                            echo Html::a(
                                    IconHelper::icon('trash'),
                                    ['translationnorm/delete', 'id'=>$norm['id']],
                                    [
                                        'title'=>Yii::t('app','Delete'),
                                        'data-method' => 'post',
                                        'data-confirm' => 'Вы действительно хотите удалить этоу норму оплаты?',
                                    ]
                            );
                        }
                    ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>