<?php

/**
 * @var View $this
 * @var array $model
 * @var ActiveDataProvider $dataProvider
 * @var LessonSearch $searchModel
 * @var string $tab
 */

use client\models\searches\LessonSearch;
use yii\bootstrap\Tabs;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\DetailView;

$this->title = Yii::$app->params['siteTitle'];
$this->params['navActiveLink'] = 'courses';
?>
<div class="content-block student-course">
    <?= Html::a(
        Html::tag('i', '', ['class' => 'fa fa-arrow-left', 'aria-hidden' => 'true']) . ' Назад к списку',
        ['student/courses'], ['class' => 'btn btn-default', 'style' => 'margin-bottom: 1rem']) ?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'title' => [
                'attribute' => 'title',
                'label' => 'Курс',
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
                'value' => function (array $data) {
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
                'value' => function ($data) {
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
    ]) ?>
    <?= Tabs::widget([
        'items' => [
            [
                'label' => Yii::t('app', 'Lessons'),
                'url' => Url::to(['student/course', 'id' => $model['id'], 'type' => 'lessons']),
                'active' => $tab === 'lessons'
            ],
//            [
//                'label' => Yii::t('app', 'Announcements'),
//                'url' => Url::to(['student/course', 'id' => $model['id'], 'type' => 'announcements']),
//                'active' => $tab === 'announcements'
//            ],
            [
                'label' => Yii::t('app', 'Files'),
                'url' => Url::to(['student/course', 'id' => $model['id'], 'type' => 'files']),
                'active' => $tab === 'files'
            ],
        ]]); ?>
    <?php if ($tab === 'lessons') {
        echo $this->render("_{$tab}", ['dataProvider' => $dataProvider, 'searchModel' => $searchModel]);
    } else if ($tab === 'files') {
        echo $this->render("_{$tab}", ['dataProvider' => $dataProvider, 'searchModel' => $searchModel]);
    } ?>
</div>