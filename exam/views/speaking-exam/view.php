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
    <?= $this->render('_exam_content', [
            'examTasks' => $examModel->tasks ?? [],
    ]) ?>
</div>
