<?php

/**
 * @var SpeakingExam $examModel
 */

use common\components\helpers\IconHelper;
use common\components\helpers\RequestHelper;
use exam\models\SpeakingExam;
use exam\models\SpeakingExamTask;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

$this->title = Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Speaking'), 'url' => ['speaking-exam/index']];
$this->params['breadcrumbs'][] = "Вариант {$examModel->num}";
?>
<div class="speaking-exam-view">
    <div class="mb-1">
        <?= Html::a(
            IconHelper::icon($examModel->enabled ? 'times' : 'check', Yii::t('app', $examModel->enabled ? 'Disable' : 'Enable')),
            ['speaking-exam/change', 'id' => $examModel->id],
            RequestHelper::createLinkPostOptions(
                ['class' => 'btn btn-' . ($examModel->enabled ? 'danger' : 'success')],
                ['SpeakingExam[enabled]' => $examModel->enabled ? 0 : 1]
            )
        ) ?>
    </div>
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
    <?php
        /** @var SpeakingExamTask $examTask */
        foreach($examModel->tasks as $examTask) { ?>
        <div class="card mb-1">
            <div class="card-body">
                <h5><b>№<?= $examTask->id ?>.</b> <?= $examTask->title ?></h5>
                <div><?= $examTask->description ?></div>
                <?php if ($examTask->images) { ?>
                    <div>
                        <?php foreach($examTask->images as $key => $imageName) {
                            echo Html::img(['site/get-exam-file', 'name' => $imageName], ['alt' => "Exam picture {$key}"]);
                        } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>
