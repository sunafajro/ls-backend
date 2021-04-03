<?php
/**
 * @var View $this
 * @var array $salaries
 * @var int $month
 * @var int $year
 * @var int|null $teacherId
 */

use common\components\helpers\DateHelper;
use common\components\helpers\IconHelper;
use school\models\Teacher;
use school\widgets\filters\models\FilterDropDown;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Reports');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Salaries report');

$date = new \DateTime();
$this->params['sidebar'] = [
    'viewFile' => '//report/_sidebar',
    'params' => [
        'actionUrl'     => ['report/salaries'],
        'items'         => [
            new FilterDropDown([
                'name'  => 'month',
                'title' => Yii::t('app', 'Month'),
                'options' => DateHelper::getMonths(),
                'prompt' => false,
                'value' => $month ?? $date->format('m'),
            ]),
            new FilterDropDown([
                'name'    => 'year',
                'title'   => Yii::t('app', 'Year'),
                'options' => DateHelper::getYears(),
                'prompt'  => false,
                'value'   => $year ?? $date->format('Y'),
            ]),
            new FilterDropDown([
                'name'    => 'teacherId',
                'title'   => Yii::t('app', 'Преподаватели'),
                'options' => Teacher::find()->select('name')->where(['visible' => 1, 'old' => 0])->indexBy('id')->orderBy(['name' => SORT_ASC])->column(),
                'prompt'  => Yii::t('app', '-all teachers-'),
                'value'   => $teacherId ?? '',
            ]),
        ],
        'hints' => [],
        'activeReport' => 'salaries',
    ]
];

if (!empty($salaries)) {
    $totalSum = 0;
    foreach ($salaries as $teacher) {
        $totalSum += $teacher['counts']['all'] ?? 0; ?>
        <div>
            <h3>
                <?= $teacher['name'] ?>
                <div class="small">
                    <span title="Всего"><?= IconHelper::icon('rub')?> <?= number_format($teacher['counts']['all'] ?? 0, 0, '.', ' ') ?></span>
                </div>
            </h3>
            <table class="table table-striped table-bordered table-hover table-condensed small">
                <thead>
                <th>№</th>
                <th>Дата</th>
                <th>Часы</th>
                <th>Ставка, р</th>
                <th>Сумма, р</th>
                </thead>
                <tbody>
                <?php foreach($teacher['rows'] ?? [] as $accrual) { ?>
                    <tr>
                        <td width="10%"><?= $accrual['id'] ?></td>
                        <td width="10%"><?= date('d.m.Y', strtotime($accrual['date'])) ?></td>
                        <td width="10%"><?= $accrual['hours'] ?></td>
                        <td width="10%"><?= $accrual['tax'] ?></td>
                        <td width="10%"><?= $accrual['sum'] ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
    <div>
        <h3 class="text-center">
            <span title="Всего"><?= IconHelper::icon('rub')?> <?= number_format($totalSum, 0, '.', ' ') ?></span>
        </h3>
    </div>
<?php }

