<?php

/**
 * @var View        $this
 * @var array       $offices
 * @var string|null $end
 * @var string|null $start
 */

use school\widgets\filters\models\FilterDateAdditionalButtons;
use school\widgets\filters\models\FilterDateInput;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Common report');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Common report');

$this->params['sidebar'] = [
    'viewFile' => '//report/_sidebar',
    'params' => [
        'actionUrl' => ['report/common'],
        'items' => [
            new FilterDateInput([
                'addClasses' => ['js--filter-start-date'],
                'name'       => 'start',
                'title'      => Yii::t('app', 'Period start'),
                'format'     => 'dd.mm.yyyy',
                'value'      => $start ?? '',
            ]),
            new FilterDateInput([
                'addClasses' => ['js--filter-end-date'],
                'name'       => 'end',
                'title'      => Yii::t('app', 'Period end'),
                'format'     => 'dd.mm.yyyy',
                'value'      => $end ?? '',
            ]),
            new FilterDateAdditionalButtons([
                'dateStartClass' => 'js--filter-start-date',
                'dateEndClass'   => 'js--filter-end-date',
                'format'         => 'dd.mm.yyyy',
            ]),
        ],
        'hints' => [],
        'activeReport' => 'common',
    ]
];
?>
<table class='table table-bordered table-stripped table-hover table-condensed small'>
    <thead>
        <tr>
            <th>Офис</th>
            <th>Оплаты</th>
            <th>Счета</th>
            <th>Скидки</th>
            <th>Начисления</th>
            <th>Часы</th>
            <th>Студенты</th>
            <th>Долги</th>
        <tr>
    </thead>
    <tbody>
        <?php foreach($offices as $report) {?>
            <tr>
                <td><?= isset($report['name']) ? $report['name'] : '' ?></td>
                <td class="text-right">
                    Нал.: <?= isset($report['payments']) && isset($report['payments']['cash']) ? number_format($report['payments']['cash'], 0, ',', ' ') : 0 ?> р.<br />
                    Терм.: <?= isset($report['payments']) && isset($report['payments']['card']) ? number_format($report['payments']['card'], 0, ',', ' ') : 0 ?> р.<br />
                    Банк.: <?= isset($report['payments']) && isset($report['payments']['bank']) ? number_format($report['payments']['bank'], 0, ',', ' ') : 0 ?> р.<br />
                    Всего: <?= isset($report['payments']) && isset($report['payments']['money']) ? number_format($report['payments']['money'], 0, ',', ' ') : 0 ?> р.
                </td>
                <td class="text-right"><?= isset($report['invoices']) ? number_format($report['invoices'], 0, ',', ' ') : 0 ?> р.</td>
                <td class="text-right"><?= isset($report['discounts']) ? number_format($report['discounts'], 0, ',', ' ') : 0 ?> р.<br />
                <?php
                    if($report['oid']==999 && $report['invoices'] > 0) {
                        echo round(($report['discounts'] * 100) / $report['invoices']) . '% от счетов';
                    }
                ?>
                </small></td>
                <td class="text-right"><?= isset($report['accruals']) ? number_format($report['accruals'], 0, ',', ' ') : 0 ?> р.</td>
                <td class="text-right">
                    <?= Html::tag(
                        'i',
                        null,
                        [
                        'class'       => 'fa fa-skype',
                        'aria-hidden' => 'true',
                        'style'       => 'margin-right: 5px',
                        'title'       => Yii::t('app', 'Online lesson'),
                        ]
                    ) ?>
                    <?= isset($report['hours_online']) ? number_format($report['hours_online'], 0, ',', ' ') : 0 ?> час.
                    /
                    <?= Html::tag(
                        'i',
                        null,
                        [
                        'class'       => 'fa fa-building',
                        'aria-hidden' => 'true',
                        'style'       => 'margin-right: 5px',
                        'title'       => Yii::t('app', 'Office lesson'),
                        ]
                    ) ?>
                    <?= isset($report['hours_office']) ? number_format($report['hours_office'], 0, ',', ' ') : 0 ?> час.
                </td>
                <td class="text-right"><?= isset($report['students']) ? number_format($report['students'], 0, ',', ' ') : 0 ?> ч.</td>
                <td class="text-right"><?= isset($report['debts']) ? number_format($report['debts'], 0, ',', ' ') : 0 ?> р.</td>
            </tr>
        <?php } ?>
    </tbody>
</table>
