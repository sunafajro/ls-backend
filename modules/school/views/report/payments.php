<?php

/**
 * @var yii\web\View $this
 * @var string|null  $end
 * @var array        $offices
 * @var string|null  $oid
 * @var array        $payments
 * @var array        $reportList
 * @var string|null  $start
 * @var string       $userInfoBlock
 */

use app\models\Moneystud;
use app\models\Notification;
use app\widgets\alert\AlertWidget;
use app\widgets\filters\FiltersWidget;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Reports');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Payments report');

$total = [
    Moneystud::PAYMENT_TYPE_CASH => 0,
    Moneystud::PAYMENT_TYPE_CARD => 0,
    Moneystud::PAYMENT_TYPE_BANK => 0,
    'all' => 0,
];
?>
<div class="row report-payments">
    <?= $this->render('_sidebar', [
            'actionUrl'     => ['report/payments'],
            'hints'         => [],
            'items'         => [
                [
                    'name'  => 'start',
                    'title' => 'Начало периода',
                    'type'  => FiltersWidget::FIELD_TYPE_DATE_INPUT,
                    'value' => $start ?? '',
                ],
                [
                    'name'  => 'end',
                    'title' => 'Конец периода',
                    'type'  => FiltersWidget::FIELD_TYPE_DATE_INPUT,
                    'value' => $end ?? '',
                ],
                [
                    'name'    => 'oid',
                    'options' => $offices ?? [],
                    'prompt'  => Yii::t('app', '-all offices-'),
                    'title'   => 'Офисы',
                    'type'    => FiltersWidget::FIELD_TYPE_DROPDOWN,
                    'value'   => $oid ?? '',
                ],
            ],
            'reportList'    => $reportList,
    ]) ?>  
    <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') {
            try {
                echo Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
                ]);
            } catch (Exception $e) {
                echo Html::tag('div', 'Не удалось отобразить виджет. ' . $e->getMessage(), ['class' => 'alert alert-danger']);
            }
        } ?>
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>
        <?php
            try {
                echo AlertWidget::widget();
            } catch (Exception $e) {
                echo Html::tag('div', 'Не удалось отобразить виджет. ' . $e->getMessage(), ['class' => 'alert alert-danger']);
            }
        ?>
        <?php foreach ($payments ?? [] as $officeId => $office) { ?>
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
                                            <?php if ($row['notification'] !== Notification::STATUS_QUEUE) { ?>
                                                <?= Html::a(
                                                    Html::tag(
                                                    'i',
                                                    '',
                                                    [
                                                        'class' => 'fa fa-paper-plane',
                                                        'aria-hidden' => 'true',
                                                    ]),
                                                    $row['notificationId'] ?
                                                        ['notification/resend', 'id' => $row['notificationId']] :
                                                        ['notification/create', 'type' => Notification::TYPE_PAYMENT, 'id' => $row['id']],
                                                    [
                                                        'data' => ['method' => 'post'],
                                                        'title' => $row['notificationId'] ? Yii::t('app', 'Resend') : Yii::t('app', 'Send'),
                                                    ]
                                                )?>
                                            <?php } ?>
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
            ?>
        <?php } ?>
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
    </div>
</div>
