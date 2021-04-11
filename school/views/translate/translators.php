<?php

/**
 * @var View $this
 * @var array $languages
 * @var array $translators
 * @var array $translatorLanguages
 * @var array $urlParams
 */

use common\components\helpers\IconHelper;
use school\models\AccessRule;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Translators');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
$this->params['breadcrumbs'][] = Yii::t('app','Translators');
$this->params['sidebar'] = ['languages' => $languages, 'urlParams' => $urlParams];

$canAddLanguage = AccessRule::checkAccess('langtranslator_create');
$canUpdate = AccessRule::checkAccess('translator_update');
$canDelete = AccessRule::checkAccess('translator_delete');
?>
<table class="table table-stripped table-bordered table-hover table-condensed small">
    <thead>
        <tr>
            <th>#</th>
            <th>ФИО</th>
            <th>Язык</th>
            <th>Телефон</th>
            <th>Э.почта</th>
            <th>Нот. заверение</th>
            <th>Ссылка</th>
            <th>Скайп</th>
            <th>Комментарии</th>
            <th><?= Yii::t('app','Act.') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($translators as $key => $t) { ?>
        <tr>
            <td><?= $key + 1 ?></td>
            <td><?= $t['name'] ?></td>
            <td>
                <?php foreach($translatorLanguages as $l) { ?>
                    <?php if ($l['tid']==$t['id']) { ?>
                        <?= $l['lname'] ?><br/>
                    <?php } ?>
                <?php } ?>
            </td>
            <td><?= $t['phone'] ?></td>
            <td><?= $t['email'] ?></td>
            <td class='text-center'><?= ($t['notarial'] == 1) ? IconHelper::icon('check') : '' ?></td>
            <td class="text-center">
                <?=$t['url'] ? Html::a('', 'http://'.$t['url'], ['class'=>'glyphicon glyphicon-new-window', 'target'=>'_blank']) : '' ?>
            </td>
            <td><?= $t['skype'] ?></td>
            <td><?= $t['description'] ?></td>
            <td class="text-center">
                <?php
                    if ($canAddLanguage) {
                        echo Html::a(
                                IconHelper::icon('language'),
                                ['langtranslator/create', 'tid' => $t['id']],
                                ['title' => Yii::t('app','Add'), 'style' => 'margin-right: 2px']
                        );
                    }
                    if ($canUpdate) {
                        echo Html::a(
                                IconHelper::icon('pencil'),
                                ['translator/update', 'id' => $t['id']],
                                ['title' => Yii::t('app','Edit'), 'style' => 'margin-right: 2px']
                        );
                    }
                    if ($canDelete) {
                        echo Html::a(
                                IconHelper::icon('trash'),
                                ['translator/delete', 'id' => $t['id']],
                                [
                                    'title' => Yii::t('app','Delete'),
                                    'data-method' => 'post',
                                    'data-confirm' => 'Вы действительно хотите удалить этого переводчика?',
                                ]
                        );
                    }
                ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>