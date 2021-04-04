<?php

/**
 * @var yii\web\View $this
 * @var array        $teacherHours
 * @var string|null  $start
 * @var string|null  $end
 * @var string|null  $teacherId
 */

use school\models\Teacher;
use school\widgets\filters\models\FilterDateInput;
use school\widgets\filters\models\FilterDropDown;
use yii\helpers\Html;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Reports');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Teacher hours');

$this->params['sidebar'] = [
    'viewFile' => '//report/_sidebar',
    'params' => [
        'actionUrl'     => ['report/teacher-hours'],
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
                'name'    => 'teacherId',
                'title'   => Yii::t('app', 'Преподаватели'),
                'options' => Teacher::find()->select('name')->where(['visible' => 1, 'old' => 0])->indexBy('id')->orderBy(['name' => SORT_ASC])->column(),
                'prompt'  => Yii::t('app', '-all teachers-'),
                'value'   => $teacherId ?? '',
            ]),
        ],
        'hints'         => [
            'При установке интервала более недели, отчет будет ограничен выборкой в 7 дней от даты начала периода.'
        ],
        'activeReport' => 'teacher-hours',
    ],
]; ?>
<table class="table table-striped table-bordered table-hover table-condensed text-center small">
    <thead>
        <th class="text-center" style="width: 5%">№</th>
        <th class="text-center" style="width: 20%"><?= Yii::t('app', 'Teacher') ?></th>
        <?php foreach (array_keys($teacherHours['hours'] ?? []) as $date) { ?>
            <th class="text-center" style="width: 10%"><?= date('d.m.Y', strtotime($date)) ?></th>
        <?php } ?>
        <th class="text-center" style="width: 5%">Итого</th>
    </thead>
    <tbody>
        <?php $i = 1; ?>
        <?php foreach ($teacherHours['teachers'] ?? [] as $id => $name) { ?>
            <tr>
                <td><?= $i ?></td>
                <td><?= Html::a($name, ['teacher/view', 'id' => $id]) ?></td>
                <?php
                    $totalHours = 0;
                    foreach (array_keys($teacherHours['hours'] ?? []) as $date) {
                    $totalHoursByDay = 0;
                    ?>
                    <td>
                        <?php foreach ($teacherHours['hours'][$date][$id] ?? [] as $lesson) { ?>
                            <div>
                                <?= $lesson['period'] ?> (<?= round($lesson['periodHours'], 2) ?>) ч.
                            </div>
                            <?php
                                $totalHours += $lesson['periodHours'] ?? 0;
                                $totalHoursByDay += $lesson['periodHours'] ?? 0;
                            ?>
                        <?php } ?>
                        <?php if (count($teacherHours['hours'][$date][$id] ?? []) > 1) { ?>
                            <hr style="margin: 0" />
                            <div class="text-center"><?= round($totalHoursByDay, 2) ?> ч.</div>
                        <?php } ?>
                    </td>
                <?php } ?>
                <td><?= round($totalHours, 2) ?> ч.</td>
            </tr>
            <?php $i++; ?>
        <?php } ?>
    </tbody>
</table>