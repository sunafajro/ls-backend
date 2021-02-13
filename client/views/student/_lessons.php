<?php

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var LessonSearch $searchModel
 */

use client\models\Journalgroup;
use client\models\searches\LessonSearch;
use client\models\Student;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

$statuses = Student::getAttendanceStatuses();

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        'date' => [
            'attribute' => 'date',
            'format' => 'raw',
            'value' => function (array $model) {
                $info = [date('d.m.Y', strtotime($model['date']))];
                switch ($model['type']) {
                    case 'online':
                        $info[] = Html::tag(
                            'i',
                            null,
                            [
                                'class' => 'fab fa-skype',
                                'aria-hidden' => 'true',
                                'title' => Yii::t('app', 'Online lesson'),
                            ]
                        );
                        break;
                    case 'office':
                        $info[] = Html::tag(
                            'i',
                            null,
                            [
                                'class' => 'fas fa-building',
                                'aria-hidden' => 'true',
                                'title' => Yii::t('app', 'Office lesson'),
                            ]
                        );
                        break;
                }
                return join(Html::tag('br'), $info);
            }
        ],
        'teacherName' => [
            'attribute' => 'teacherName',
        ],
        'subject' => [
            'attribute' => 'subject',
            'format' => 'raw',
            'label' => Yii::t('app', 'Subject') . '/' . Yii::t('app', 'Homework'),
            'value' => function (array $model) {
                return Html::tag(
                    'div',
                    $model['subject'],
                    ['class' => 'small', 'style' => 'margin-bottom: 1rem']
                ) . Html::tag(
                    'div',
                    Html::tag('b', 'Д/з:') . ' ' . $model['hometask'],
                    ['class' => 'small']
                );
            },
        ],
        'comments' => [
            'attribute' => 'comments',
            'label' => Yii::t('app', 'Comments/Recommendations'),
        ],
        'successes' => [
            'attribute' => 'successes',
            'format' => 'raw',
            'label' => Yii::t('app', 'Count of "successes"'),
            'value' => function (array $model) {
                return $model['successes'] ? join('', Student::prepareStudentSuccessesList((int)$model['successes'])) : '';
            }
        ],
        'status' => [
            'attribute' => 'status',
            'format' => 'raw',
            'label' => Yii::t('app', 'Status'),
            'value' => function ($model) use ($statuses) {
                $color = (int)$model['status'] === Student::STUDENT_STATUS_PRESENT
                    ? 'success'
                    : ((int)$model['status'] === Student::STUDENT_STATUS_ABSENT_WARNED
                        ? 'info'
                        : 'danger');
                $items = [
                    Html::tag('div', ($statuses[$model['status']] ?? $model['status']), ['class' => "label label-{$color}"])
                ];
                return join('', $items);
            }
        ],
    ],
]);
