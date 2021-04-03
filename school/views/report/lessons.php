<?php

/**
 * @var View               $this
 * @var LessonSearch       $searchModel
 * @var ActiveDataProvider $dataProvider
 * @var string             $actionUrl
 * @var string|null        $end
 * @var string|null        $start
 */

use school\models\Journalgroup;
use school\models\Office;
use school\models\searches\LessonSearch;
use school\widgets\filters\models\FilterDateInput;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app', 'Lessons report');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Lessons report');

$this->params['sidebar'] = [
        'viewFile' => '//report/_sidebar',
        'params' => [
            'actionUrl' => $actionUrl,
            'items' => [
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
            ],
            'hints'         => [
                'Столбец Группа поддерживает фильтрацию как по названию группы так и по её номеру.',
                'Столбец Комментарии отображает только студентов присутствовавших на занятии.',
                'Статистика по студентам учитывает всех студентов отмесенных в журналах групп.'
            ],
            'activeReport' => 'lessons',
    ],
];

$statistics = $searchModel->getAttendanceStatistics(); ?>
<h3 class="text-center">
    <span style="margin-right: 10px">
        <i class="fa fa-user" aria-hidden="true"></i>
        Отметок в журнале: <?= $statistics['present'] ?? 0 ?>,
    </span>
    <span style="margin-right: 10px">
        <i class="fa fa-user-o" aria-hidden="true"></i>
        Уникальных студентов: <?= $statistics['presentReal'] ?? 0 ?>.
    </span>
</h3>
<?php
    try {
        $offices = Office::find()->select(['name'])->active()->indexBy('id')->orderBy(['name' => SORT_ASC])->column();
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{pager}\n{items}\n{pager}",
            'columns' => [
                'id' => [
                    'attribute' => 'id',
                    'format' => 'raw',
                    'headerOptions' => ['width' => '5%'],
                    'label' => '№',
                    'value' => function (array $model) {
                        $type = '';
                        switch ($model['type']) {
                            case Journalgroup::TYPE_ONLINE:
                                $type = Html::tag(
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
                                $type = Html::tag(
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
                        return join(Html::tag('br'), [Html::a($model['id'], ['groupteacher/view', 'id' => $model['groupId'], 'lid' => $model['id']]), $type]);
                    }
                ],
                'date' => [
                    'attribute' => 'date',
                    'format' => ['date', 'php:d.m.Y'],
                    'headerOptions' => ['width' => '5%'],
                    'label' => Yii::t('app', 'Date'),
                ],
                'teacherName' => [
                    'attribute' => 'teacherName',
                    'format' => 'raw',
                    'headerOptions' => ['width' => '15%'],
                    'label' => Yii::t('app', 'Teacher'),
                    'value' => function ($model) {
                        return Html::a($model['teacherName'], ['teacher/view', 'id' => $model['teacherId']]);
                    },
                ],
                'groupName' => [
                    'attribute' => 'groupName',
                    'format' => 'raw',
                    'headerOptions' => ['width' => '15%'],
                    'label' => Yii::t('app', 'Group'),
                    'value' => function ($model) {
                        return Html::a('№' . $model['groupId'] . ' ' . $model['groupName'], ['groupteacher/view', 'id' => $model['groupId']]);
                    },
                ],
                'subject' => [
                    'attribute' => 'subject',
                    'headerOptions' => ['width' => '15%'],
                    'label' => Yii::t('app', 'Subject'),
                ],
                'hometask' => [
                    'attribute' => 'hometask',
                    'headerOptions' => ['width' => '15%'],
                    'label' => Yii::t('app', 'Homework'),
                ],
                'comments' => [
                    'attribute' => 'comments',
                    'format' => 'raw',
                    'label' => Yii::t('app', 'Comments'),
                    'value' => function ($model) use ($searchModel) {
                        $commentsArr = $searchModel->getCommentsByLesson($model['id']);
                        $comments = [];
                        foreach ($commentsArr as $comment) {
                            $comments[] = Html::tag(
                                    'p',
                                    join('', [
                                        Html::a($comment['studentName'], ['studname/view', 'id' => $comment['studentId']]),
                                        Html::tag('br'),
                                        Html::tag('i', isset($comment['comment']) && trim($comment['comment']) !== '' ? $comment['comment'] : '(пусто)'),
                                        $comment['successes'] ? ' (' . join('', Journalgroup::prepareStudentSuccessesList((int)$comment['successes'])) . ')' : '',
                                    ])
                            );
                        }
                        $first = array_shift($comments);
                        $result = [
                            $first,
                        ];
                        if (!empty($comments)) {
                            $result[] = Html::tag('div', join('', $comments), ['id' => "collapseComments_{$model['id']}", 'class' => 'collapse']);
                            $result[] = Html::a(
                                    'развернуть/свернуть (' . count($comments) . ')...',
                                    "#collapseComments_{$model['id']}",
                                    [
                                        'class'         => 'small',
                                        'role'          => 'button',
                                        'data-toggle'   => 'collapse',
                                        'aria-expanded' => 'false',
                                        'aria-controls' => "collapseComments_{$model['id']}",
                                    ]
                            );
                        }
                        return join('', $result);
                    }
                ],
                'officeId' => [
                    'attribute' => 'officeId',
                    'filter' => $offices,
                    'format' => 'raw',
                    'value' => function ($model) use ($offices) {
                        return $offices[$model['officeId']] ?? $model['officeId'];
                    }
                ]
            ],
        ]);
    } catch (Exception $e) {
        echo Html::tag('div', 'Не удалось отобразить виджет. ' . $e->getMessage(), ['class' => 'alert alert-danger']);
    }