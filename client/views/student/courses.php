<?php

/**
 * @var View $this
 * @var ArrayDataProvider $dataProvider
 */

use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->params['siteTitle'];
$this->params['navActiveLink'] = 'courses';
?>
<div class="content-block student-groups">
    <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => SerialColumn::class],
                'title' => [
                    'attribute' => 'title',
                    'format' => 'raw',
                    'label' => 'Курсы',
                    'value' => function(array $data) {
                        return Html::a($data['title'], ['student/course', 'id' => $data['id']]);
                    }
                ],
                'levels' => [
                    'attribute' => 'levels',
                    'label' => 'Уровень',
                ],
                'startDate' => [
                    'attribute' => 'startDate',
                    'format' => ['date', 'php:d.m.Y'],
                    'label' => 'Дата начала',
                ],
                'endDates' => [
                    'attribute' => 'endDates',
                    'label' => 'Дата окончания',
                    'value' => function(array $data) {
                        $dates = explode(',', $data['endDates']);
                        $date = '-';
                        if (count($dates) === count(array_filter($dates))) {
                            $date = max($dates);
                            $date = date('d.m.Y', strtotime($date));
                        }
                        return $date;
                    },
                ],
                'timeNorm' => [
                    'attribute' => 'timeNorm',
                    'label' => 'Продолжительность',
                    'value' => function($data) {
                        return "{$data['timeNorm']} ч.";
                    }
                ],
                'lessonsCount' => [
                    'attribute' => 'lessonsCount',
                    'label' => 'Посещено занятий',
                ],
                'offices' => [
                    'attribute' => 'offices',
                    'label' => 'Офис',
                ],
            ],
    ])?>
</div>