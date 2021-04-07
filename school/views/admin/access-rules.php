<?php

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var AccessRuleSearch $searchModel
 * @var array $menuLinks
 */

use common\components\helpers\IconHelper;
use school\models\Role;
use school\models\searches\AccessRuleSearch;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' .  Yii::t('app','Access rules');
$this->params['breadcrumbs'][] = [ 'url' => ['admin/index'], 'label' => Yii::t('app','Administration')];
$this->params['breadcrumbs'][] = Yii::t('app','Access rules');

$this->params['sidebar'] = ['menuLinks' => $menuLinks];

$roles = Role::find()->select('name')->indexBy('id')->orderBy('id')->column();
echo  GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        'id',
        'controller',
        'action',
        'role_id' => [
            'attribute' => 'role_id',
            'filter' => $roles,
            'value' => function($model) use ($roles) {
                return $roles[$model->role_id] ?? '-';
            }
        ],
        'userName',
        [
            'class' => ActionColumn::class,
            'header' => Yii::t('app', 'Act.'),
            'template' => '{update} {delete}',
            'buttons' => [
                'update' => function ($url, $model) {
                    return Html::a(
                        IconHelper::icon('edit'),
                        Url::to(['access-rule/update', 'id' => $model->id])
                    );
                },
                'delete' => function ($url, $model) {
                    return Html::a(
                        IconHelper::icon('trash'),
                        Url::to(['access-rule/delete', 'id' => $model->id]),
                        ['data-method' => 'post', 'data-confirm' => 'Вы действительно хотите удалить роль?']
                    );
                },
            ],
        ],
    ],
]);