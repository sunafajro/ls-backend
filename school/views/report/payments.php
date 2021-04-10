<?php

/**
 * @var yii\web\View $this
 * @var array        $payments
 * @var string|null  $start
 * @var string|null  $end
 * @var string|null  $officeId
 */

use school\models\AccessRule;
use school\models\Moneystud;
use school\models\Notification;
use school\models\Office;
use school\widgets\filters\models\FilterDateInput;
use school\widgets\filters\models\FilterDropDown;
use yii\helpers\Html;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Reports');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Payments report');

$total = [
    Moneystud::PAYMENT_TYPE_CASH => 0,
    Moneystud::PAYMENT_TYPE_CARD => 0,
    Moneystud::PAYMENT_TYPE_BANK => 0,
    'all' => 0,
];

$this->params['sidebar'] = [
    'viewFile' => '//report/_sidebar',
    'params' => [
        'actionUrl'     => ['report/payments'],
        'items'         => [
            new FilterDateInput([
                'name'  => 'start',
                'title' => Yii::t('app', 'Period start'),
                'format' => 'dd.mm.yyyy',
                'value' => $start ?? '',
            ]),
            new FilterDateInput([
                'name'  => 'end',
                'title' => Yii::t('app', 'Period end'),
                'format' => 'dd.mm.yyyy',
                'value' => $end ?? '',
            ]),
            new FilterDropDown([
                'name'    => 'officeId',
                'options' => Office::find()->select(['name'])->active()->indexBy('id')->orderBy(['name' => SORT_ASC])->column(),
                'prompt'  => Yii::t('app', '-all offices-'),
                'title'   => Yii::t('app', 'Offices'),
                'value'   => $officeId ?? '',
            ]),
        ],
        'hints' => [],
        'activeReport' => 'payments',
    ]
];
if (!empty($payments)) {
    foreach ($payments ?? [] as $officeId => $office) { ?>
        <div>
            <h3>
                <?= $office['name'] ?? NULL ?>&nbsp;&nbsp;
                <small>
                    <span title="<?= Yii::t('app', 'Cash') ?>">
                        <i class="fa fa-money" aria-hidden="true"></i>
                        <?= isset($office['counts']['cash']) ? number_format(round($office['counts']['cash']), 0, '.', ' ') : NULL ?>
                    </span>&nbsp;&nbsp;
                    <span title="<?= Yii::t('app', 'Card') ?>">
                        <i class="fa fa-credit-card" aria-hidden="true"></i>
                        <?= isset($office['counts']['card']) ? number_format(round($office['counts']['card']), 0, '.', ' ') : NULL ?>
                    </span>&nbsp;&nbsp;
                    <span title="<?= Yii::t('app', 'Bank') ?>">
                        <i class="fa fa-university" aria-hidden="true"></i>
                        <?= isset($office['counts']['bank']) ? number_format(round($office['counts']['bank']), 0, '.', ' ') : NULL ?>
                    </span>&nbsp;&nbsp;
                    <span title="<?= Yii::t('app', 'Total') ?>">
                        <i class="fa fa-rub" aria-hidden="true"></i>
                        <?= isset($office['counts']['all']) ? number_format(round($office['counts']['all']), 0, '.', ' ') : NULL ?>
                    </span>
                </small>
            </h3>
            <?php foreach ($office ?? [] as $dateId => $date) { ?>
                <?php if (!in_array($dateId, ['name', 'counts'])) { ?>
                <div>
                    <h4>
                        <?= date('d.m.Y', strtotime($dateId)) ?>&nbsp;&nbsp;
                        <small>
                            <span title="<?= Yii::t('app', 'Cash') ?>">
                                <i class="fa fa-money" aria-hidden="true"></i>
                                <?= isset($date['counts']['cash']) ? number_format(round($date['counts']['cash']), 0, '.', ' ') : NULL ?>
                            </span>&nbsp;&nbsp;
                            <span title="<?= Yii::t('app', 'Card') ?>">
                                <i class="fa fa-credit-card" aria-hidden="true"></i>
                                <?= isset($date['counts']['card']) ? number_format(round($date['counts']['card']), 0, '.', ' ') : NULL ?>
                            </span>&nbsp;&nbsp;
                            <span title="<?= Yii::t('app', 'Bank') ?>">
                                <i class="fa fa-university" aria-hidden="true"></i>
                                <?= isset($date['counts']['bank']) ? number_format(round($date['counts']['bank']), 0, '.', ' ') : NULL ?>
                            </span>&nbsp;&nbsp;
                            <span title="<?= Yii::t('app', 'Total') ?>">
                                <i class="fa fa-rub" aria-hidden="true"></i>
                                <?= isset($date['counts']['all']) ? number_format(round($date['counts']['all']), 0, '.', ' ') : NULL ?>
                            </span>
                        </small>
                    </h4>
                    <table class="table table-striped table-bordered table-hover table-condensed small">
                        <thead>
                            <th style="width: 5%">№</th>
                            <th style="width: 30%"><?= Yii::t('app', 'Student') ?></th>
                            <th style="width: 30%"><?= Yii::t('app', 'Manager') ?></th>
                            <th style="width: 10%"><?= Yii::t('app', 'Receipt') ?></th>
                            <th style="width: 10%"><?= Yii::t('app', 'Type') ?></th>
                            <th style="width: 10%"><?= Yii::t('app', 'Sum') ?></th>
                            <th style="width: 5%"><?= Yii::t('app', 'Status') ?></th>
                        </thead>
                        <tbody>
                            <?php foreach($date['rows'] ?? [] as $row) { ?>
                            <tr class="<?= (int)$row['active'] === 0 ? 'danger' : '' ?>">
                                <?php foreach($row ?? [] as $colId => $colVal) { ?>
                                    <?php if (!in_array($colId, ['studentId', 'active', 'remain', 'notificationId', 'notification'])) { ?>
                                        <td
                                          class="<?= $colId === 'type' ? Moneystud::getPaymentTypeColorClass($colVal) : '' ?>"
                                          style="<?= (int)$row['active'] === 0 ? 'text-decoration: line-through' : '' ?>"
                                        >
                                            <?php if ($colId === 'student') { ?>
                                                <?= Html::a($colVal, ['studname/view', 'id' => $row['studentId']]) ?>
                                            <?php } else if ($colId === 'sum') { ?>
                                                <?= number_format($colVal, 2, '.', ' ') ?>
                                            <?php } else if ($colId === 'type') { ?>
                                                <?= Moneystud::getPaymentTypeLabel($colVal) ?>
                                            <?php } else { ?>
                                                <?= $colVal ?>
                                            <?php } ?>
                                        </td>
                                    <?php } ?>
                                <?php } ?>
                                <td>
                                    <?php if ((int)$row['active'] === 1) { ?>
                                        <?php if ($row['notificationId']) { ?>
                                            <?= Html::tag(
                                                'i',
                                                '',
                                                [
                                                    'class' => 'fa fa-envelope ' . Notification::getTextColorClassByStatus($row['notification'] ?? ''),
                                                    'aria-hidden' => 'true',
                                                    'title' => Yii::t('app', Notification::getStatusLabel($row['notification'] ?? '')),
                                                ]
                                            )?>
                                        <?php } ?>
                                        <?php if ($row['notification'] !== Notification::STATUS_QUEUE) {
                                            if (!$row['notificationId']) {
                                                if (AccessRule::checkAccess('notification_create')) {
                                                    echo Html::a(
                                                        Html::tag(
                                                            'i',
                                                            '',
                                                            [
                                                                'class' => 'fa fa-paper-plane',
                                                                'aria-hidden' => 'true',
                                                            ]),
                                                        ['notification/create', 'type' => Notification::TYPE_PAYMENT, 'id' => $row['id']],
                                                        [
                                                            'data' => ['method' => 'post'],
                                                            'title' => Yii::t('app', 'Send'),
                                                        ]
                                                    );
                                                }
                                            } else {
                                                if (AccessRule::checkAccess('notification_resend')) {
                                                    echo Html::a(
                                                        Html::tag(
                                                            'i',
                                                            '',
                                                            [
                                                                'class' => 'fa fa-paper-plane',
                                                                'aria-hidden' => 'true',
                                                            ]),
                                                        ['notification/resend', 'id' => $row['notificationId']],
                                                        [
                                                            'data' => ['method' => 'post'],
                                                            'title' => Yii::t('app', 'Resend'),
                                                        ]
                                                    );
                                                }
                                            }
                                        } ?>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php } ?>
            <?php } ?>
        </div>
        <?php
            $total[Moneystud::PAYMENT_TYPE_CASH] += $office['counts'][Moneystud::PAYMENT_TYPE_CASH] ?? 0;
            $total[Moneystud::PAYMENT_TYPE_CARD] += $office['counts'][Moneystud::PAYMENT_TYPE_CARD] ?? 0;
            $total[Moneystud::PAYMENT_TYPE_BANK] += $office['counts'][Moneystud::PAYMENT_TYPE_BANK] ?? 0;
            $total['all'] += $office['counts']['all'] ?? 0;
    } ?>
    <h3 class="text-center">
        <span title="<?= Yii::t('app', 'Cash') ?>">
            <i class="fa fa-money" aria-hidden="true"></i>
            <?= isset($total[Moneystud::PAYMENT_TYPE_CASH]) ? number_format(round($total[Moneystud::PAYMENT_TYPE_CASH]), 0, '.', ' ') : NULL ?>
        </span>&nbsp;&nbsp;
        <span title="<?= Yii::t('app', 'Card') ?>">
            <i class="fa fa-credit-card" aria-hidden="true"></i>
            <?= isset($total[Moneystud::PAYMENT_TYPE_CARD]) ? number_format(round($total[Moneystud::PAYMENT_TYPE_CARD]), 0, '.', ' ') : NULL ?>
        </span>&nbsp;&nbsp;
        <span title="<?= Yii::t('app', 'Bank') ?>">
            <i class="fa fa-university" aria-hidden="true"></i>
            <?= isset($total[Moneystud::PAYMENT_TYPE_BANK]) ? number_format(round($total[Moneystud::PAYMENT_TYPE_BANK]), 0, '.', ' ') : NULL ?>
        </span>&nbsp;&nbsp;
        <span title="<?= Yii::t('app', 'Total') ?>">
            <i class="fa fa-rub" aria-hidden="true"></i>
            <?= isset($total['all']) ? number_format(round($total['all']), 0, '.', ' ') : NULL ?>
        </span>
    </h3>
<?php }
