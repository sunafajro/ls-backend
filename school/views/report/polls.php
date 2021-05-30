<?php

/**
 * @var yii\web\View $this
 * @var ActiveDataProvider $dataProvider
 * @var string|null $start
 * @var string|null $end
 * @var Poll|null $poll
 * @var array $totals
 */

use common\models\BasePoll as Poll;
use school\widgets\filters\models\FilterDateInput;
use school\widgets\filters\models\FilterDropDown;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Reports');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Polls');

$this->params['sidebar'] = [
    'viewFile' => '//report/_sidebar',
    'params' => [
        'actionUrl'     => ['report/polls'],
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
            new FilterDropDown([
                'name'    => 'pollId',
                'title'   => Yii::t('app', 'Polls'),
                'options' => Poll::find()->select(['title'])->indexBy('id')->orderBy(['title' => SORT_ASC])->column(),
                'prompt'  => false,
                'value'   => $poll->id ?? '',
            ]),
        ],
        'hints'         => [],
        'activeReport' => 'polls',
    ],
];

if (!empty($poll)) {
    echo Html::tag('h3', 'Опрос: ' .  $poll->title);
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
            ],
            'date' => [
                'attribute' => 'date',
                'label' => Yii::t('app','Date'),
                'format' => ['date', 'php:d.m.Y'],
            ],
            'studentName' => [
                'attribute' => 'studentName',
                'format' => 'raw',
                'label' => Yii::t('app', 'Student'),
                'value' => function (array $model) {
                    return Html::a($model['studentName'], ['studname/view', 'id' => $model['studentId']]);
                },
            ],
            'responses' => [
                'attribute' => 'responses',
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:60%'],
                'label' => Yii::t('app', 'Responses'),
                'value' => function(array $model) use ($poll) {
                    return $this->render('poll/_poll', ['poll' => $poll, 'studentId' => $model['studentId'], 'responseId' => $model['id']]);
                }
            ],
        ],
    ]);
    echo $this->render('poll/_totals', ['poll' => $poll, 'totals' => $totals]);
}

