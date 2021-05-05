<?php

/**
 * @var yii\web\View $this
 * @var ActiveDataProvider $dataProvider
 * @var string|null  $start
 * @var string|null  $end
 */

use school\widgets\filters\models\FilterDateInput;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Reports');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Logins');

$this->params['sidebar'] = [
    'viewFile' => '//report/_sidebar',
    'params' => [
        'actionUrl'     => ['report/logins'],
        'items'         => [
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
        'hints'         => [],
        'activeReport' => 'logins',
    ],
];
echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id' => [
                'attribute' => 'id',
                'label' => 'ID',
            ],
            'name' => [
                'attribute' => 'name',
                'format' => 'raw',
                'label' => Yii::t('app', 'Full name'),
                'value' => function (array $model) {
                    return Html::a($model['name'], ['studname/view', 'id' => $model['id']]);
                },
            ],
            'count' => [
                'attribute' => 'count',
                'label' => Yii::T('app', 'Login count'),
            ],
        ],
]);

