<?php
/**
 * @var View              $this
 * @var Student           $student
 * @var ArrayDataProvider $detailData
 * @var int               $type
 */

use school\models\Student;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$num = [
    'attribute' => 'num',
    'label' => Yii::t('app', 'Lesson count') . ', шт.',
];
if (!in_array($type, [Student::DETAIL_TYPE_PAYMENTS])) {
    $num['footer'] = Student::calculateDetailTotal($detailData->allModels, 'num');
}
$sum = [
    'attribute' => 'sum',
    'label' => Yii::t('app', 'Sum') . ', руб.',
    'value' => function (array $data) {
        return $data['sum'] !== '' ?
            number_format($data['sum'], 2, ',', ' ')
            : '';
    }
];
if (!in_array($type, [Student::DETAIL_TYPE_LESSONS])) {
    $sum['footer'] = Student::calculateDetailTotal($detailData->allModels, 'sum', true);
}

echo GridView::widget([
    'dataProvider' => $detailData,
    'columns' => [
        'id' => [
            'attribute' => 'id',
            'format' => 'raw',
            'label' => Yii::t('app', 'ID'),
            'value' => function (array $data) use ($student) {
                $tab = 3;
                if ($data['type'] === 'payment') {
                    $tab = 4;
                }
                if ($data['type'] === 'lesson') {
                    $tab = 6;
                }
                return Html::a(
                    $data['id'],
                    Url::to(['studname/view', 'id' => $student->id, 'tab' => $tab])
                );
            },
        ],
        'date' => [
            'attribute' => 'date',
            'format' => ['date', 'php:d.m.Y'],
            'label' => Yii::t('app', 'Date'),
        ],
        'type' => [
            'attribute' => 'type',
            'contentOptions' => function (array $data) {
                $color = 'warning';
                if ($data['type'] === 'payment') {
                    $color = 'success';
                }
                if ($data['type'] === 'lesson') {
                    $color = 'info';
                }
                return ['class' => "$color text-center"];
            },
            'format' => 'raw',
            'label' => Yii::t('app', 'Type'),
            'value' => function (array $data) {
                return Yii::t('app', $data['type']);
            }
        ],
        'name' => [
            'attribute' => 'name',
            'label' => Yii::t('app', 'Name'),
        ],
        'num' => $num,
        'sum' => $sum,
        'comment' => [
            'attribute' => 'comment',
            'label' => Yii::t('app', 'Receipt'),
        ],
    ],
    'showFooter' => true,
]);