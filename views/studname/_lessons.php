<?php

use app\models\Journalgroup;
use app\models\search\LessonSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

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
        return date('d.m.Y', strtotime($model['date'])) . Html::tag('br') . Html::tag('small', "(#{$model['id']})");
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
    'value'     => function ($model) use ($statuses, $studentId) {
        $color = (int)$model['status'] === Journalgroup::STUDENT_STATUS_PRESENT
            ? 'success'
            : ((int)$model['status'] === Journalgroup::STUDENT_STATUS_ABSENT_WARNED
                ? 'info'
                : 'danger');
        $items = [
            Html::tag('div',  ($statuses[$model['status']] ?? $model['status']), ['class' => "label label-{$color}"])
        ];
        if (in_array((int)Yii::$app->session->get('user.ustatus'), [3, 4]) && (int)$model['status'] === Journalgroup::STUDENT_STATUS_ABSENT_UNWARNED) {
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
