<?php

/**
 * @var View       $this
 * @var array      $groups
 * @var array      $lessons
 * @var array      $teacherLessonsCount
 * @var array      $teacherNames
 * @var string     $corporate
 * @var string     $officeId
 * @var string     $teacherId
 * @var string     $page
 * @var Pagination $pages
 */

use common\components\helpers\IconHelper;
use school\models\Journalgroup;
use school\models\Office;
use school\models\Teacher;
use school\widgets\filters\models\FilterCheckbox;
use school\widgets\filters\models\FilterDropDown;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Journals report');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Journals report');

$this->params['sidebar'] = [
    'viewFile' => '//report/_sidebar',
    'params' => [
        'actionUrl' => ['report/journals'],
        'items' => [
            new FilterCheckbox([
                'name'   => 'corporate',
                'title'  => Yii::t('app', 'Corporate lessons'),
                'value'  => $corporate ?? '',
            ]),
            new FilterDropDown([
                'name'    => 'officeId',
                'title'   => Yii::t('app', 'Offices'),
                'options' => Office::find()->select(['name'])->active()->indexBy('id')->orderBy(['name' => SORT_ASC])->column(),
                'prompt'  => Yii::t('app', '-all offices-'),
                'value'   => $officeId ?? '',
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
        'activeReport' => 'journals',
    ],
];
if ($teacherNames) {
    $start = 1;
    $end = 10;
    $nextpage = 2;
    $prevpage = 0;
    if ($page) {
        if ($page > 1) {
            $start = (10 * ($page - 1) + 1);
            $end = $start + 9;
            if ($end >= $pages->totalCount) {
                $end = $pages->totalCount;
            }
            $prevpage = $page - 1;
            $nextpage = $page + 1;
        }
    }

    $prevPageButton = (($prevpage > 0) ? Html::a('Предыдущий', ['report/journals', 'page' => $prevpage, 'teacherId' => $teacherId, 'corporate' => $corporate, 'officeId' => $officeId], ['class' => 'btn btn-default']) : '');
    $pagerInfo = Html::tag('p', 'Показано ' . $start . ' - ' . ($end >= $pages->totalCount ? $pages->totalCount : $end) . ' из ' . $pages->totalCount, ['style' => 'margin-top: 1rem; margin-bottom: 0.5rem']);
    $nextPageButton = (($end < $pages->totalCount) ? Html::a('Следующий', ['report/journals', 'page' => $nextpage, 'teacherId' => $teacherId, 'corporate' => $corporate, 'officeId' => $officeId], ['class' => 'btn btn-default']) : '');

    $pagerBlock = Html::tag(
        'div',
        join('', [
            Html::tag('div', $prevPageButton, ['class' => 'col-xs-12 col-sm-3 text-left']),
            Html::tag('div', $pagerInfo, ['class' => 'col-xs-12 col-sm-6 text-center']),
            Html::tag('div', $nextPageButton, ['class' => 'col-xs-12 col-sm-3 text-right']),
        ]),
        ['class' => 'row', 'style' => 'margin-bottom: 0.5rem']
    );

    echo $pagerBlock;

    foreach ($teacherNames as $key => $value) { ?>
        <div class="bg-info" style="padding: 10px; height: 40px">
            <div class="pull-left">
                <b><?= $value ?></b>
            </div>
            <div class="pull-right">
                <span class="label label-info" title="Количество занятий на проверке"><?= $teacherLessonsCount[$key]['totalCount'] ?></span>
            </div>
        </div>
        <?php foreach ($groups as $g) { ?>
            <?php if ((int)$g['tid'] === (int)$key) { ?>
                <div style="padding: 10px">
                    <?= Html::a("#{$g['gid']} {$g['service']}, ур: {$g['ename']} (усл.#{$g['sid']})", ['groupteacher/view', 'id' => $g['gid']]) ?>
                </div>
                <?php if ($teacherLessonsCount[$key][$g['gid']]['totalCount'] > 0) { ?>
                    <table class="table table-bordered table-stripped table-hover table-condensed' style='margin-bottom:10px">
                        <tbody>
                        <?php foreach ($lessons as $l) { ?>
                            <?php if ((int)$l['gid'] === (int)$g['gid'] && (int)$key === (int)$l['tid']) { ?>
                                <tr <?= ((int)$l['visible'] === 0 ? 'class="danger"' : '') ?>>
                                    <td width="5%">
                                        #<?= $l['lid'] ?>
                                        <?php
                                        switch ($l['type']) {
                                            case Journalgroup::TYPE_ONLINE:
                                                echo Html::tag(
                                                    'i',
                                                    null,
                                                    [
                                                        'class' => 'fa fa-skype',
                                                        'aria-hidden' => 'true',
                                                        'style' => 'margin-right: 5px',
                                                        'title' => Yii::t('app', 'Online lesson'),
                                                    ]
                                                );
                                                break;
                                            case Journalgroup::TYPE_OFFICE:
                                                echo Html::tag(
                                                    'i',
                                                    null,
                                                    [
                                                        'class' => 'fa fa-building',
                                                        'aria-hidden' => 'true',
                                                        'style' => 'margin-right: 5px',
                                                        'title' => Yii::t('app', 'Office lesson'),
                                                    ]
                                                );
                                                break;
                                        }
                                        ?>
                                    </td>
                                    <td width="2%"><?= ((int)$l['done'] === 1 ? IconHelper::icon('check') : '') ?></td>
                                    <td width="15%"><?= Html::a($l['date'] . ' →', ['groupteacher/view', 'id' => $l['gid'], '#' => 'lesson_' . $l['lid']]) ?></td>
                                    <td><?= $l['desc'] ?></td>
                                    <td width="5%"><?= $g['hours'] ?> ч.</td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                        </tbody>
                    </table>
                <?php }
            }
        }
    }

    echo $pagerBlock;
}
