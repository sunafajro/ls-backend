<?php

/**
 * @var SpeakingExam $examModel
 */

use exam\models\SpeakingExam;
use yii\widgets\DetailView;

$this->title = Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Speaking'), 'url' => ['speaking-exam/index']];
$this->params['breadcrumbs'][] = "Вариант {$examModel->num}";
?>
<div class="speaking-exam-view">
    <?= DetailView::widget([
        'model' => $examModel,
        'attributes' => [
            'id',
            'num' => [
                'attribute' => 'num',
                'value' => function(SpeakingExam $exam) {
                    return "Вариант {$exam->num}";
                },
            ],
            'waitTime',
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
                },
            ],
        ]
    ]) ?>
    <h3><?= Yii::t('app', 'Tasks')?>:</h3>
    <?php foreach($examModel->tasks as $examTask) { ?>
        <div class="card mb-1">
            <div class="card-body">
                <h5><b>№<?= $examTask->id ?>.</b> <?= $examTask->title ?></h5>
                <div><?= $examTask->description ?></div>
            </div>
        </div>
    <?php } ?>
</div>
