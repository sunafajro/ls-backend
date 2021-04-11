<?php

/**
 * @var View $this
 * @var array $languages
 * @var array $urlParams
 */

use common\components\helpers\IconHelper;
use school\models\AccessRule;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Languages');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
$this->params['breadcrumbs'][] = Yii::t('app','Languages');
$this->params['sidebar'] = ['urlParams' => $urlParams];

$canUpdate = AccessRule::checkAccess('translationlang_update');
$canDelete = AccessRule::checkAccess('translationlang_delete');
?>
<table class="table table-stripped table-hover table-condensed table-bordered small">
    <thead>
        <tr>
            <th>№</th>
            <th><?= Yii::t('app','Name') ?></th>
            <th><?= Yii::t('app','Act.') ?></th>
    </thead>
    <tbody>
        <?php foreach($languages as $key => $lang) { ?>
            <tr>
                <td class="tbl-cell-5"><?= $key + 1 ?></td>
                <td><?= $lang->name ?></td>
                <td class="tbl-cell-5 text-center">
                    <?php
                        if ($canUpdate) {
                            echo Html::a(
                                IconHelper::icon('pencil'),
                                ['translationlang/update', 'id'=>$lang->id],
                                ['title'=>Yii::t('app','Edit'), 'style' => 'margin-right: 2px']
                            );
                        }
                        if ($canDelete) {
                            echo Html::a(
                                IconHelper::icon('trash'),
                                ['translationlang/delete', 'id'=>$lang->id],
                                [
                                    'title'=>Yii::t('app','Delete'),
                                    'data-method' => 'post',
                                    'data-confirm' => 'Вы действительно хотите удалить этот язык?',
                                ]
                            );
                        }
                    ?>
                </td>
            </tr>
            <?php } ?>
    </tbody>
</table>