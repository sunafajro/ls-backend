<?php

/**
 * @var View  $this
 * @var array $languages
 * @var array $translations
 * @var array $urlParams
 * @var array $years
 */

use common\components\helpers\IconHelper;
use school\models\Auth;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Translations');
$this->params['breadcrumbs'][] = Yii::t('app','Translations');
/** @var Auth $auth */
$auth = \Yii::$app->user->identity;
$this->params['sidebar'] = ['languages' => $languages, 'years' => $years, 'urlParams' => $urlParams, 'canCreate' => in_array($auth->roleId, [3, 9])];

$sum = 0;
?>
<table class="table table-stripped table-bordered table-hover table-condensed small">
    <thead>
        <tr>
            <th>#</th>
            <th>Срок обращ.</th>
            <th>Окончание</th>
            <th>Заказчик</th>
            <th>Исполнитель</th>
            <th>Напр. перевода</th>
            <th>Наим. файла</th>
            <th>Стоим. за 1 уч.ед.</th>
            <th>Кол-во п.з. / у.е.</th>
            <th>Сумма, р</th>
            <th>Примеч.</th>
            <th>Счет</th>
            <?php if (in_array($auth->roleId, [3, 9])) { ?>
                <th><?= Yii::t('app','Act.') ?></th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
    <?php foreach($translations as $key => $t) { ?>
        <tr>
            <td><?= $key + 1 ?></td>
            <td><?= date('d.m.y', strtotime($t['tdate'])) ?></td>
            <td><?= ($t['tenddate']!=='0000-00-00' ? date('d.m.y', strtotime($t['tenddate'])) : '') ?></td>
            <td><?= $t['client'] ?></td>
            <td><?= $t['translator'] ?></td>
            <td><?= mb_substr($t['from_lang'], 0, 3)."-".mb_substr($t['to_lang'],0,3) ?></td>
            <td><?= $t['nomination'] ?></td>
            <td><?= $t['tnorm'] ?></td>
            <td><?= number_format($t['pscount'], 0, ',', ' ') ?><br/><?= number_format($t['aucount'], 2, ',', ' ') ?></td>
            <td><?= number_format($t['value'], 2, ',', ' ') ?></td>
            <td><?= $t['desc'] ?></td>
            <td><?= $t['receipt'] ?></td>
            <?php if (in_array($auth->roleId, [3, 9])) { ?>
                <td class="text-center">
                    <?= Html::a(IconHelper::icon('pencil'), ['translation/update', 'id'=>$t['tid']], ['title'=>Yii::t('app','Edit')]) ?>
                    <?= Html::a(IconHelper::icon('trash'), ['translation/disable', 'id'=>$t['tid']], ['title'=>Yii::t('app','Delete')]) ?>
                </td>
            <?php } ?>
        </tr>
        <?php $sum = $sum + $t['value']; ?>
    <?php } ?>
    </tbody>
</table>
<div class="text-right"><strong>Итого: <?= number_format($sum, 2, ',', ' ') ?></strong></div>
<?php
    $percent = 5;
    $proc = 0.05;
    if ($sum>60000) {
        $percent = 10;
        $proc = 0.1;
    }
?>
<div class='text-right'><strong><?= $percent ?>%: <?= number_format($sum * $proc, 2, ',', ' ') ?></strong></div>