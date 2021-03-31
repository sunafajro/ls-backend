<?php

use school\models\Invoicestud;
use common\widgets\alert\AlertWidget;
use school\models\Office;
use school\widgets\filters\FiltersWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/**
 * @var View        $this
 * @var array       $dates
 * @var array       $invoices
 * @var string|null $start
 * @var string|null $end
 * @var string|null $officeId
 */

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Reports');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Invoices report');

$this->params['sidebar'] = [
    'viewFile' => '//report/_sidebar',
    'params' => [
        'actionUrl'     => ['report/invoices'],
        'items'         => [
            [
                'name'  => 'start',
                'title' => 'Начало периода',
                'type'  => FiltersWidget::FIELD_TYPE_DATE_INPUT,
                'format' => 'dd.mm.yyyy',
                'value' => $start ?? '',
            ],
            [
                'name'  => 'end',
                'title' => 'Конец периода',
                'type'  => FiltersWidget::FIELD_TYPE_DATE_INPUT,
                'format' => 'dd.mm.yyyy',
                'value' => $end ?? '',
            ],
            [
                'name'    => 'officeId',
                'options' => Office::find()->select(['name'])->active()->indexBy('id')->orderBy(['name' => SORT_ASC])->column(),
                'prompt'  => Yii::t('app', '-all offices-'),
                'title'   => 'Офисы',
                'type'    => FiltersWidget::FIELD_TYPE_DROPDOWN,
                'value'   => $officeId ?? '',
            ],
        ],
        'hints' => [],
        'activeReport' => 'invoices',
    ],
];
$totalsum = 0;
foreach ($dates as $key => $value) { ?>
    <a
      href="#collapse-invoice-<?= $key ?>"
      role="button"
      data-toggle="collapse" aria-expanded="false"
      aria-controls="collapse-invoice-<?= $key ?>"
      class="text-warning">
        <?= date('d.m.y', strtotime($value)) ?> (<?= Yii::t('app', date('l', strtotime($value))) ?>)
    </a>
    <br />
    <div class="collapse" id="collapse-invoice-<?= $key ?>">
    <?php $totaldaysum = 0; ?>
        <table class="table table-bordered table-stripped table-hover table-condensed">
            <tbody>
            <?php foreach ($invoices as $inv) { ?>
                <?php if ($inv['date'] === $value) { ?>
                    <?php if ((int)$inv['visible'] === 0) { ?>
                        <tr class="danger">
                    <?php } else { ?>
                        <?php if ((int)$inv['done'] === 1) { ?>
                            <tr class="success">
                        <?php } else { ?>
                            <tr class="warning">
                        <?php } ?>
                    <?php } ?>
                    <td>
                        #<?= $inv['iid'] ?>
                        <?= ((int)$inv['remain'] === Invoicestud::TYPE_REMAIN ? ' (остаточный)' : '') ?>
                        <?= ((int)$inv['remain'] === Invoicestud::TYPE_NETTING ? ' (взаимозачет)' : '') ?>
                    </td>
                    <td><?= $inv['uname'] ?></td>
                    <td><?= Html::a($inv['sname'] . " → ", ['studname/view', 'id' => $inv['sid']]) ?> (усл. #<?= $inv['id'] ?>, <?= $inv['num'] ?> зан.)</td>
                    <td><?= $inv['money'] ?></td>
                    </tr>
                    <?php if ((int)$inv['visible'] === 1 && (int)$inv['remain'] === Invoicestud::TYPE_NORMAL) { ?>
                        <?php $totaldaysum = $totaldaysum + $inv['money']; ?>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <p class="text-right">всего за день: <?= $totaldaysum ?></p>
    <?php $totalsum = $totalsum + $totaldaysum;
} ?>
<hr />
<p class="text-right">всего по офису: <?= $totalsum ?></p>