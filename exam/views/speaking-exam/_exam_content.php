<?php

/**
 * @var View $this
 * @var SpeakingExamTask[] $examTasks
 */

use exam\models\SpeakingExamTask;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\web\View;
?>
<div>
    <h3><?= Yii::t('app', 'Tasks')?>:</h3>
    <?php
    /** @var SpeakingExamTask $examTask */
    foreach($examTasks as $examTask) { ?>
        <div class="card mb-1">
            <div class="card-body">
                <h5><b>№<?= $examTask->id ?>.</b> <?= $examTask->title ?></h5>
                <?php if ($examTask->images) { ?>
                    <div>
                        <?php foreach($examTask->images as $key => $imageName) {
                            echo Html::img(['site/get-exam-file', 'name' => $imageName], ['class' => 'img-thumbnail mr-1', 'alt' => "Exam picture {$key}"]);
                        } ?>
                    </div>
                <?php } ?>
                <div>
                    <?php if ($examTask->audio ?? false) {
                        echo Html::tag(
                                'audio',
                                Html::tag('source', null, ['src' => Url::to(['site/get-exam-file', 'name' => $examTask->description]), 'type' => 'audio/mpeg']),
                                ['controls' => true]
                        );
                    } else {
                        echo $examTask->description;
                    } ?></div>
                <?php if ($examTask->questions) { ?>
                    <div>
                        <?php if ($examTask->audio ?? false) {
                            $question = reset($examTask->questions);
                            echo Html::tag(
                                    'audio',
                                    Html::tag('source', null, ['src' => Url::to(['site/get-exam-file', 'name' => $question]), 'type' => 'audio/mpeg']),
                                    ['controls' => true]
                            );
                        } else { ?>
                            <ul>
                                <?php foreach($examTask->questions as $key => $question) {
                                    echo Html::tag('li', $question);
                                } ?>
                            </ul>
                        <?php } ?>
                        <?php if ($examTask->note ?? false) {
                            echo Html::tag('p', $examTask->note);
                        } ?>
                    </div>
                <?php } ?>
                <div>
                    <?php if ($examTask->sequentialQuestions) { ?>
                        <p class="mb-1"><b><?= $examTask->getAttributeLabel('questionsInterval') . ':' ?></b> <?= $examTask->questionsInterval ?> <?= \Yii::t('app', 'sec') ?></p>
                    <?php } ?>
                    <p class="mb-1"><b><?= $examTask->getAttributeLabel('prepareTime') . ':' ?></b> <?= $examTask->prepareTime ?> <?= \Yii::t('app', 'sec') ?></p>
                    <p class="mb-1"><b><?= $examTask->getAttributeLabel('recordTime') . ':' ?></b> <?= $examTask->recordTime ?> <?= \Yii::t('app', 'sec') ?></p>
                </div>
            </div>
        </div>
    <?php } ?>
</div>