<?php

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var GroupFileSearch $searchModel
 */

use client\models\GroupFile;
use client\models\searches\GroupFileSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\web\View;

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => SerialColumn::class],
        'original_name' => [
            'attribute' => 'original_name',
            'format' => 'raw',
            'value' => function (GroupFile $file) {
                return Html::a($file->original_name, [
                    'files/download',
                    'id' => $file->id
                ], ['target' => '_blank']);
            }
        ],
        'size' => [
            'attribute' => 'size',
            'format' => ['shortSize', 2],
        ],
        'create_date' => [
            'attribute' => 'create_date',
            'format' => ['date', 'php:d.m.Y'],
        ],
    ],
]) ?>
