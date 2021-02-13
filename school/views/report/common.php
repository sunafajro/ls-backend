<?php

use common\widgets\alert\AlertWidget;
use school\widgets\filters\FiltersWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/**
 * @var View        $this
 * @var array       $actionUrl
 * @var array       $commonReport
 * @var string|null $end
 * @var array       $reportList
 * @var string|null $start
 * @var string      $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Common report');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Common report');
?>
<div class="row report-common">
    <?= $this->render('_sidebar', [
        'actionUrl'     => $actionUrl,
        'items'         => [
            [
                'addClasses' => ['js--filter-start-date'],
                'name'       => 'start',
                'title'      => 'Начало периода',
                'type'       => FiltersWidget::FIELD_TYPE_DATE_INPUT,
                'value'      => $start ?? '',
            ],
            [
                'addClasses' => ['js--filter-end-date'],
                'name'       => 'end',
                'title'      => 'Конец периода',
                'type'       => FiltersWidget::FIELD_TYPE_DATE_INPUT,
                'value'      => $end ?? '',
            ],
            [
                'dateStartClass' => 'js--filter-start-date',
                'dateEndClass'   => 'js--filter-end-date',
                'type'           => FiltersWidget::ADDITIONAL_DATE_BUTTONS,
            ]
        ],
        'hints'         => [],
        'reportList'    => $reportList ?? [],
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
                <?php foreach($commonReport as $report) {?>
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
	</div>
</div>
