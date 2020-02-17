<?php

use app\models\search\LessonSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

/**
 * @var View               $this
 * @var ActiveDataProvider $dataProvider
 * @var LessonSearch       $searchModel
 */

$roleId = (int)Yii::$app->session->get('user.ustatus');

$columns = [];
$columns['date'] = [
    'attribute' => 'date',
    'format'    => 'raw',
    'value'     => function ($model) {
        return date('d.m.Y', strtotime($model['date'])) . Html::tag('br') . Html::tag('small', "(#{$model['id']})");
    }
];
if ($roleId !== 5) {
    $columns['teacherName'] = [
        'attribute' => 'teacherName',
        'format'    => 'raw',
        'value'     => function ($model) {
            return Html::a($model['teacherName'], ['teacher/view', 'id' => $model['teacherId']]);
        },
    ];
}
$columns['groupName'] = [
    'attribute' => 'groupName',
    'format'    => 'raw',
    'label'     => Yii::t('app', 'Group'),
    'value'     => function ($model) {
        return Html::a('№' . $model['groupId'] . ' ' . $model['groupName'], ['groupteacher/view', 'id' => $model['groupId']]);
    },
];
$columns['subject'] = [
    'attribute' => 'subject',
    'format'    => 'raw',
    'label'     => Yii::t('app', 'Subject') . '/' . Yii::t('app', 'Homework'),
    'value'     => function ($model) {
        return $model['subject'] . Html::tag('br') . Html::tag('small', $model['hometask']);
    },
];
$columns['comments'] = [
    'attribute' => 'comments',
    'label'     => Yii::t('app', 'Comments'),
];

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $columns,
]);