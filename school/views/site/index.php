<?php

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var NewsSearch $searchModel
 */

use school\models\searches\NewsSearch;
use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\widgets\ListView;

$this->title =  Yii::$app->name . ' :: ' . Yii::t('app', 'News');
$this->params['breadcrumbs'][] = Yii::t('app', 'News');

$this->params['sidebar'] = ['searchModel' => $searchModel];

echo ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => 'lists/_news',
]);