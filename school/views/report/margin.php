<?php

/**
 * @var View $this
 * @var array $marginReport
 * @var string|null $end
 * @var string|null $start
 */

use common\components\helpers\AlertHelper;
use school\widgets\filters\FiltersWidget;
use yii\web\View;

$this->title = \Yii::$app->name . ' :: ' . Yii::t('app','Margin report');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Margin report');

$this->params['sidebar'] = [
    'viewFile' => '//report/_sidebar',
    'params' => [
        'actionUrl' => ['report/margin'],
        'items' => [
            [
                'addClasses' => ['js--filter-start-date'],
                'name'       => 'start',
                'title'      => 'Начало периода',
                'type'       => FiltersWidget::FIELD_TYPE_DATE_INPUT,
                'format'     => 'dd.mm.yyyy',
                'value'      => $start ?? '',
            ],
            [
                'addClasses' => ['js--filter-end-date'],
                'name'       => 'end',
                'title'      => 'Конец периода',
                'type'       => FiltersWidget::FIELD_TYPE_DATE_INPUT,
                'format'     => 'dd.mm.yyyy',
                'value'      => $end ?? '',
            ],
        ],
        'hints' => [],
        'activeReport' => 'margin',
    ]
];

if (!empty($marginReport)) { ?>
		<table class="table table-hover table-striped table-condensed table-bordered small">
			<thead>
				<tr>
					<th>№</th>
					<th><?= Yii::t('app', 'Teacher') ?></th>
					<th class="text-center"><?= Yii::t('app', 'Lesson count') ?></th>
					<th class="text-center"><?= Yii::t('app', 'Income (w/out discounts)') ?>, руб.</th>
					<th class="text-center"><?= Yii::t('app', 'Payment') ?>, руб.</th>
					<th class="text-center"><?= Yii::t('app', 'Margin') ?>, руб.</th>
					<th class="text-center"><?= Yii::t('app', 'Margin') ?>, %</th>
					<th class="text-center"><?= Yii::t('app', 'Margin/Lesson') ?>, руб.</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($marginReport as $key => $t) { ?>
                    <tr>
                        <td><?= $key + 1 ?></td>
                        <td><?= $t['teacher_name'] ?></td>
                        <td class="text-center"><?= $t['lesson_count'] ?></td>
                        <td class="text-center"><?= number_format($t['sum_income'], 2, ',', ' ') ?></td>
                        <td class="text-center"><?= number_format($t['sum_accrual'], 2, ',', ' ') ?></td>
                        <td class="text-center"><?= number_format($t['sum_income'] - $t['sum_accrual'], 2, ',', ' ') ?></td>
                        <td class="text-center">
                            <?= $t['sum_income'] > 0 ? round(100 * ($t['sum_income'] - $t['sum_accrual']) / $t['sum_income']) : 0 ?>%
                        </td>
                        <td class="text-center">
                            <?= number_format(($t['sum_income'] - $t['sum_accrual'])/$t['lesson_count'], 2, ',', ' ') ?>
                        </td>
                    </tr>
				<?php } ?>
			</tbody>
		</table>
		    <?php } else {
		        echo AlertHelper::alert('По вашему запросу ничего не найдено...', 'warning');
        	} ?>
	</div>
</div>
