<?php

/**
 * @var View  $this
 * @var ActiveDataProvider $dataProvider
 * @var UserSearch $searchModel
 * @var array $offices
 * @var array $roles
 * @var array $statuses
 * @var array $urlParams
 * @var array $users
 */

use common\components\helpers\IconHelper;
use school\models\AccessRule;
use school\models\searches\UserSearch;
use school\models\User;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Users');
$this->params['breadcrumbs'][] = Yii::t('app','Users');
$this->params['sidebar'] = [];

$canView    = AccessRule::checkAccess('user_view');
$canUpdate  = AccessRule::checkAccess('user_update');
$canRestore = AccessRule::checkAccess('user_restore');
$canRemove  = AccessRule::checkAccess('user_remove');
$canDelete  = AccessRule::checkAccess('user_delete');

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        'id',
        'name',
        'login',
        'status' => [
            'attribute' => 'status',
            'filter' => $roles,
            'value' => function(User $user) use ($roles) {
                return $roles[$user->status] ?? '-';
            },
        ],
        'calc_office' => [
            'attribute' => 'calc_office',
            'filter' => $offices,
            'value' => function(User $user) use ($offices) {
                return $offices[$user->calc_office] ?? '-';
            },
        ],
        'visible' => [
            'attribute' => 'visible',
            'filter' => $statuses,
            'value' => function(User $user) use ($statuses) {
                return $statuses[$user->visible] ?? '-';
            },
        ],
        [
            'class' => ActionColumn::class,
            'header' => Yii::t('app', 'Act.'),
            'template' => '{view} {update} {restore} {remove} {delete}',
            'buttons' => [
                'restore' => function($url, $model, $key) {
                    if (intval($model->visible) === 0) {
                        return Html::a(
                            IconHelper::icon('check'),
                            ['user/restore', 'id' => $model->id],
                            ['title' => Yii::t('app', 'Enable user'), 'data-method' => 'post']
                        );
                    }
                    return null;
                },
                'remove' => function($url, $model, $key) {
                    if (intval($model->visible) === 1) {
                        return Html::a(
                            IconHelper::icon('times'),
                            ['user/remove', 'id' => $model->id],
                            ['title' => Yii::t('app', 'Disable user'), 'data-method' => 'post']
                        );
                    }
                    return null;
                }
            ],
            'visibleButtons' => [
                'view' => $canView,
                'update' => $canUpdate,
                'restore' => $canRestore,
                'remove' => $canRemove,
                'delete' => $canDelete,
            ],
        ],
    ],
]);