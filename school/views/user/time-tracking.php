<?php

/**
 * @var View                   $this
 * @var ActiveDataProvider     $dataProvider
 * @var UserTimeTrackingSearch $searchModel
 * @var User                   $user
 * @var UserTimeTrackingForm   $timeTrackingForm
 * @var array                  $can
 */

use common\components\helpers\IconHelper;
use school\assets\UserViewAsset;
use school\models\searches\UserTimeTrackingSearch;
use school\models\User;
use school\models\forms\UserTimeTrackingForm;
use school\models\UserTimeTracking;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: Учет рабочего времени';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $user->name, 'url' => ['user/view', 'id' => $user->id]];
$this->params['breadcrumbs'][] = 'Учет рабочего времени';

UserViewAsset::register($this);

if ($can['createTimeTracking']) {
    $this->params['sidebar'] = $this->render('time-tracking/_form', [
        'model'  => $timeTrackingForm,
        'userId' => $user->id,
    ]);
}
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'columns' => [
        [
            'class' => SerialColumn::class,
        ],
        'type' => [
            'attribute' => 'type',
            'filter' => UserTimeTracking::getTypeLabels(),
            'value' => function ($model) {
                return UserTimeTracking::getTypeLabel($model->type);
            }
        ],
        'start' => [
            'attribute' => 'start',
            'format' => ['date', 'php:d.m.Y H:i'],
        ],
        'end' => [
            'attribute' => 'end',
            'format' => ['date', 'php:d.m.Y H:i'],
        ],
        'comment',
        [
            'class' => ActionColumn::class,
            'header' => Yii::t('app', 'Act.'),
            'buttons' => [
                'update' => function ($url, UserTimeTracking $model) use ($can, $user) {
                    return $can['updateTimeTracking']
                        ? Html::a(
                            IconHelper::icon('pencil'),
                            ['user/time-tracking', 'id' => $user->id, 'time_tracking_id' => $model->id],
                            [
                                'title' => Yii::t('app', 'Update'),
                            ]
                        )
                        : null ;
                },
                'delete' => function ($url, UserTimeTracking $model) use ($can, $user) {
                    return $can['deleteTimeTracking']
                        ? Html::a(
                            IconHelper::icon('trash'),
                            ['user/time-tracking', 'id' => $user->id, 'action' => 'delete', 'time_tracking_id' => $model->id],
                            [
                                'title' => Yii::t('app', 'Delete'),
                                'data' => [
                                    'method' => 'post',
                                    'confirm' => 'Вы действительно хотите удалить запись?',
                                ],
                            ]
                        )
                        : null;
                }
            ],
            'template' => '{update} {delete}',
            'visible'  => $can['updateTimeTracking'] || $can['deleteTimeTracking'],
        ],
    ],
]);
