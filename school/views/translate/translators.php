<?php

/**
 * @var View $this
 * @var array $languages
 * @var array $translators
 * @var array $translatorLanguages
 * @var array $urlParams
 */

use common\components\helpers\IconHelper;
use school\models\Auth;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Translators');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
$this->params['breadcrumbs'][] = Yii::t('app','Translators');
/** @var Auth $auth */
$auth = \Yii::$app->user->identity;
$this->params['sidebar'] = [
    'viewFile' => '//translate/sidebars/_translators',
    'params' => ['languages' => $languages, 'urlParams' => $urlParams, 'canCreate' => in_array($auth->roleId, [3, 9])],
];
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
            <?php if (in_array($auth->roleId, [3, 9])) { ?>
                <th><?= Yii::t('app','Act.') ?></th>
            <?php } ?>
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
            <?php if (in_array($auth->roleId, [3, 9])) { ?>
                <td class="text-center">
                <?= Html::a(IconHelper::icon('language'), ['langtranslator/create', 'tid' => $t['id']], ['title' => Yii::t('app','Add')]) ?>
                <?= Html::a(IconHelper::icon('pencil'), ['translator/update', 'id' => $t['id']], ['title' => Yii::t('app','Edit')]) ?>
                <?= Html::a(IconHelper::icon('trash'), ['translator/disable', 'id' => $t['id']], ['title' => Yii::t('app','Delete')]) ?>
                </td>
            <?php } ?>
        </tr>
    <?php } ?>
    </tbody>
</table>