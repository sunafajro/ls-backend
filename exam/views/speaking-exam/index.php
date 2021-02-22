<?php

/**
 * @var View $this
 * @var ArrayDataProvider $dataProvider
 */

use common\components\helpers\IconHelper;
use common\components\helpers\RequestHelper;
use common\widgets\alert\AlertB4Widget;
use exam\models\SpeakingExam;
use yii\bootstrap4\Html;
use yii\data\ArrayDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\web\View;

$this->title = Yii::$app->name;
$this->params['breadcrumbs'][] = Yii::t('app', 'Speaking');
?>
<div class="speaking-exam-index">
    <?= AlertB4Widget::widget() ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id' => [
                'attribute' => 'id',
                'format' => 'raw',
                'value' => function(SpeakingExam $exam) {
                    return Html::a($exam->id, ['speaking-exam/view', 'id' => $exam->id]);
                }
            ],
            'num' => [
                'attribute' => 'num',
                'value' => function(SpeakingExam $exam) {
                    return "Вариант {$exam->num}";
                },
            ],
            'tasks' => [
                'attribute' => 'tasks',
                'value' => function(SpeakingExam $exam) {
                    return count($exam->tasks);
                }
            ],
            'waitTime' => [
                'attribute' => 'waitTime',
                'value' => function(SpeakingExam $exam) {
                    return "{$exam->waitTime} с";
                },
            ],
            'type' => [
                'attribute' => 'type',
                'value' => function(SpeakingExam $exam) {
                    return SpeakingExam::getTypeLabel($exam->type);
                }
            ],
            'enabled' => [
                'attribute' => 'enabled',
                'value' => function(SpeakingExam $exam) {
                    return Yii::t('app', $exam->enabled ? 'Yes' : 'No');
                }
            ],
            [
                'class' => ActionColumn::class,
                'header' => Yii::t('app', 'Actions'),
                'buttons' => [
                    'toggle' => function ($url, $model, $key) {
                        return  Html::a(
                                IconHelper::icon($model->enabled ? 'times' : 'check'),
                                ['speaking-exam/change', 'id' => $model->id],
                                RequestHelper::createLinkPostOptions(
                                        ['title' => Yii::t('app', $model->enabled ? 'Disable' : 'Enable')],
                                        ['SpeakingExam[enabled]' => $model->enabled ? 0 : 1]
                                )
                        );
                    },
                    'update' => function ($url, $model, $key) {
                        return  Html::a(
                            IconHelper::icon('edit'),
                            ['speaking-exam/update', 'id' => $model->id],
                            ['title' => Yii::t('app', 'Edit')]
                        );
                    },
                    'delete' => function ($url, $model, $key) {
                        return  Html::a(
                            IconHelper::icon('trash'),
                            ['speaking-exam/delete', 'id' => $model->id],
                            RequestHelper::createLinkPostOptions(
                                    ['title' => Yii::t('app', 'Delete')],
                                    null,
                                    Yii::t('app', 'Do you really want to remove the exam?')
                            )
                        );
                    },
                ],
                'template' => '{toggle} {update} {delete}'
            ]
        ]
    ]) ?>
</div>
