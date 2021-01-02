<?php

use app\models\Invoicestud;
use app\widgets\alert\AlertWidget;
use app\widgets\filters\FiltersWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/**
 * @var View        $this
 * @var array       $dates
 * @var string|null $end
 * @var array       $invoices
 * @var array       $offices
 * @var string|null $oid
 * @var array       $reportList
 * @var string|null $start
 * @var string      $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Reports');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Invoices report');
?>
<div class="report-invoices">
    <?= $this->render('_sidebar', [
            'actionUrl'     => ['report/invoices'],
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
        <?php $totalsum = 0; ?>
        <?php foreach ($dates as $key => $value) { ?>
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
        <?php $totalsum = $totalsum + $totaldaysum; ?>
        <?php } ?>
        <hr />
        <p class="text-right">всего по офису: <?= $totalsum ?></p>
    </div>
</div>