<?php

use school\models\Office;
use school\models\StudentCommission;
use common\widgets\alert\AlertWidget;
use school\widgets\filters\FiltersWidget;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/**
 * @var View               $this
 * @var StudentCommission  $searchModel
 * @var ActiveDataProvider $dataProvider
 * @var string             $actionUrl
 * @var string             $end
 * @var array              $offices
 * @var array              $reportList
 * @var string             $start
 * @var string             $userInfoBlock
 */

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Reports');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Commissions');

$this->params['sidebar'] = [
    'viewFile' => '//report/_sidebar',
    'params' => [
        'actionUrl' => $actionUrl,
        'items' => [
            [
                'name'  => 'start',
                'title' => 'Начало периода',
                'type'  => FiltersWidget::FIELD_TYPE_DATE_INPUT,
                'format' => 'dd.mm.yyyy',
                'value' => $start ?? '',
            ],
            [
                'name'  => 'end',
                'title' => 'Конец периода',
                'type'  => FiltersWidget::FIELD_TYPE_DATE_INPUT,
                'format' => 'dd.mm.yyyy',
                'value' => $end ?? '',
            ],
        ],
        'hints'     => [
            'При фильтрации по столбцу Дата, фильтр по периоду игнорируется.',
        ],
        'activeReport' => 'commissions',
    ]
];
try {
    $offices = Office::find()->select(['name'])->active()->indexBy('id')->orderBy(['name' => SORT_ASC])->column();
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'layout'       => "{pager}\n{items}\n{pager}",
        'columns'      => [
            'id' => [
                'attribute' => 'id',
                'headerOptions' => ['width' => '5%'],
                'label' => '№',
            ],
            'date' => [
                'attribute' => 'date',
                'format'    => ['date', 'php:d.m.Y'],
                'headerOptions' => ['width' => '5%'],
                'label'     => Yii::t('app', 'Date'),
            ],
            'studentName' => [
                'attribute' => 'studentName',
                'format'    => 'raw',
                'headerOptions' => ['width' => '15%'],
                'label'     => Yii::t('app', 'Student'),
                'value'     => function ($model) {
                    return Html::a($model['studentName'], ['studname/view', 'id' => $model['studentId']]);
                },
            ],
            'value' => [
                'attribute' => 'value',
                'format' => 'raw',
                'headerOptions' => ['width' => '15%'],
                'label'     => Yii::t('app', 'Sum'),
                'value'     => function ($model) {
                    $value = [];
                    $value[] = number_format($model['value'] ?? 0, 2, '.', ' ') . ' руб.';
                    if ($model['percent'] > 0) {
                        $value[] = Html::tag('small', "{$model['percent']}% от долга {$model['debt']} руб.");
                    }
                    return join(Html::tag('br'), $value);
                },
            ],
            'comment' => [
                'attribute' => 'comment',
                'headerOptions' => ['width' => '15%'],
                'label'     => Yii::t('app', 'Comment'),
            ],
            'userName' => [
                'attribute' => 'userName',
                'format'    => 'raw',
                'headerOptions' => ['width' => '15%'],
                'label'     => Yii::t('app', 'Created by'),
                'value'     => function ($model) {
                    return $model['userName'];
                },
            ],
            'officeId' => [
                'attribute' => 'officeId',
                'filter' => $offices,
                'headerOptions' => ['width' => '15%'],
                'label' => Yii::t('app', 'Office'),
                'value' => function (array $model) use ($offices) {
                    return $offices[$model['officeId']] ?? $model['officeId'];
                },
                'visible' => in_array((int)Yii::$app->session->get('user.ustatus'), [3, 8]),
            ],
        ],
    ]);
} catch (Exception $e) {
    echo Html::tag('div', 'Не удалось отобразить виджет. ' . $e->getMessage(), ['class' => 'alert alert-danger']);
}