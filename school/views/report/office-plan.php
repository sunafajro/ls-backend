<?php
/**
 * @var View $this
 * @var Office $office
 * @var array $groupList
 * @var float $lessonPlan
 * @var float $moneyPlan
 * @var string $monthName
 */

use common\components\helpers\DateHelper;
use school\models\Office;
use school\widgets\filters\models\FilterCheckbox;
use school\widgets\filters\models\FilterDropDown;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: '.Yii::t('app','Reports');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => 'report/index'];
$this->params['breadcrumbs'][] = Yii::t('app','Plan for office');

$this->params['sidebar'] = [
    'viewFile' => '//report/_sidebar',
    'params' => [
        'actionUrl'     => ['report/office-plan'],
        'items'         => [
            new FilterCheckbox([
                'name'   => 'nextMonth',
                'title'  => Yii::t('app', 'Next month'),
                'value'  => $nextMonth ?? '',
            ]),
            new FilterDropDown([
                'name'    => 'officeId',
                'title'   => Yii::t('app', 'Offices'),
                'options' => Office::find()->select(['name'])->active()->indexBy('id')->orderBy(['name' => SORT_ASC])->column(),
                'prompt'  => false,
                'value'   => $officeId ?? '',
            ]),
        ],
        'hints' => [],
        'additionalInfo' => Html::tag('div', join('', [
            Html::tag('p',
                Html::tag('b', 'Занятий по расписанию:') . " {$lessonPlan} шт."),
            Html::tag('p',
                Html::tag('b', 'Оплат по занятиям:') . ' ' . number_format($moneyPlan, 2, ',', ' ') . ' р.'),
        ])),
        'activeReport' => 'office-plan',
    ]
];
?>
<h4>План на <?= $monthName ?> по офису <?= $office->name ?></h4>

<table class="table table-hover table-bordered table-striped table-condensed text-center small">
    <thead>
        <tr>
            <th>Группа</th>
            <th>Стоимость, р.</th>
            <th>День недели</th>
            <th>Колич. занятий, шт.</th>
            <th>Колич. учеников, ч.</th>
            <th>Всего занятий, шт.</th>
            <th>Итого, р.</th>
        </tr>
    </thead>
    <?php foreach($groupList as $group) { ?>
        <tr>
            <td><?= $group['group'] ?></td>
            <td><?= $group['cost'] ?></td>
            <td><?= DateHelper::getDayName($group['day']) ?></td>
            <td><?= $group['cnt'] ?></td>
            <td><?= $group['pupils'] ?></td>
            <td><?= $group['totalCount'] ?></td>
            <td><?= number_format($group['totalCost'], 2, ',', ' ') ?></td>
        </tr>
    <?php } ?>
</table>