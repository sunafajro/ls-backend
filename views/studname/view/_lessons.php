<?php

use app\models\Journalgroup;
use app\models\search\LessonSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View               $this
 * @var ActiveDataProvider $dataProvider
 * @var LessonSearch       $searchModel
 * @var int                $studentId
 */

$roleId = (int)Yii::$app->session->get('user.ustatus');
$statuses = Journalgroup::getAttendanceAllStatuses();
$columns = [];
$columns['date'] = [
    'attribute' => 'date',
    'format'    => 'raw',
    'value'     => function (array $model) {
        $info = [];
        switch ($model['type']) {
            case Journalgroup::TYPE_ONLINE:
                $info[] = Html::tag(
                    'i',
                    null,
                    [
                        'class'       => 'fa fa-skype',
                        'aria-hidden' => 'true',
                        'style'       => 'margin-right: 5px',
                        'title'       => Yii::t('app', 'Online lesson'),
                    ]
                );
                break;
            case Journalgroup::TYPE_OFFICE:
                $info[] = Html::tag(
                    'i',
                    null,
                    [
                        'class'       => 'fa fa-building',
                        'aria-hidden' => 'true',
                        'style'       => 'margin-right: 5px',
                        'title'       => Yii::t('app', 'Office lesson'),
                    ]
                );
                break;
        }
        $info[] = "(#{$model['id']})";
        return date('d.m.Y', strtotime($model['date'])) . Html::tag('br') . Html::tag('small', join('', $info));
    }
];
if ($roleId !== 5) {
    $columns['teacherName'] = [
        'attribute' => 'teacherName',
        'format'    => 'raw',
        'value'     => function (array $model) {
            return Html::a($model['teacherName'], ['teacher/view', 'id' => $model['teacherId']]);
        },
    ];
}
$columns['groupName'] = [
    'attribute' => 'groupName',
    'format'    => 'raw',
    'label'     => Yii::t('app', 'Group'),
    'value'     => function (array $model) {
        return Html::a('№' . $model['groupId'] . ' ' . $model['groupName'], ['groupteacher/view', 'id' => $model['groupId']]);
    },
];
$columns['subject'] = [
    'attribute' => 'subject',
    'format'    => 'raw',
    'label'     => Yii::t('app', 'Subject') . '/' . Yii::t('app', 'Homework'),
    'value'     => function (array $model) {
        return $model['subject'] . Html::tag('br') . Html::tag('small', $model['hometask']);
    },
];
$columns['comments'] = [
    'attribute' => 'comments',
    'label'     => Yii::t('app', 'Comments'),
];
$columns['status'] = [
    'attribute' => 'status',
    'format'    => 'raw',
    'label'     => Yii::t('app', 'Status'),
    'value'     => function ($model) use ($statuses, $studentId, $roleId) {
        $color = (int)$model['status'] === Journalgroup::STUDENT_STATUS_PRESENT
            ? 'success'
            : ((int)$model['status'] === Journalgroup::STUDENT_STATUS_ABSENT_WARNED
                ? 'info'
                : 'danger');
        $items = [
            Html::tag('div',  ($statuses[$model['status']] ?? $model['status']), ['class' => "label label-{$color}"])
        ];
        if (in_array($roleId, [3, 4]) && (int)$model['status'] === Journalgroup::STUDENT_STATUS_ABSENT_UNWARNED) {
            $items[] = Html::a(
                Html::tag('span', null, ['class' => 'fa fa-times', 'aria-hidden' => 'true']),
                ['journalgroup/absent', 'id' => $model['id'], 'studentId' => $studentId],
                ['data-method' => 'post', 'title' => Yii::t('app', 'To absent (was ill)')]
            );
        }
        return join('', $items);
    }
];

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $columns,
]);
