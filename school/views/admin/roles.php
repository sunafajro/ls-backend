<?php

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var RoleSearch $searchModel
 * @var array $menuLinks
 */

use common\components\helpers\IconHelper;
use school\models\AccessRule;
use school\models\searches\RoleSearch;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' .  Yii::t('app','Roles');
$this->params['breadcrumbs'][] = [ 'url' => ['admin/index'], 'label' => Yii::t('app','Administration')];
$this->params['breadcrumbs'][] = Yii::t('app','Roles');

$this->params['sidebar'] = ['menuLinks' => $menuLinks];
echo  GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        'id',
        'name',
        [
            'class' => ActionColumn::class,
            'header' => Yii::t('app', 'Act.'),
            'template' => '{update} {delete}',
            'buttons' => [
                'update' => function ($url, $model) {
                    return Html::a(
                        IconHelper::icon('edit'),
                        Url::to(['role/update', 'id' => $model->id])
                    );
                },
                'delete' => function ($url, $model) {
                    return Html::a(
                        IconHelper::icon('trash'),
                        Url::to(['role/delete', 'id' => $model->id]),
                        ['data-method' => 'post', 'data-confirm' => 'Вы действительно хотите удалить роль?']
                    );
                },
            ],
            'visibleButtons' => [
                'update' => AccessRule::checkAccess('role_update'),
                'delete' => AccessRule::checkAccess('role_delete'),
            ],
        ],
    ],
]);