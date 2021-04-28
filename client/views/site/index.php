<?php

/**
 * @var View $this
 * @var array $messages
 * @var array $comments
 * @var Poll $poll
 */

use client\models\Student;
use common\components\helpers\IconHelper;
use common\models\BasePoll as Poll;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ListView;

$this->title = Yii::$app->params['siteTitle'];
?>
<div class="content-block">
    <div class="row">
        <div class="col-xs-12 col-sm-9">
            <div class="poll-block">
                <?= $this->render('poll/_poll', ['poll' => $poll]) ?>
            </div>
            <?php
                try {
                    echo ListView::widget([
                        'dataProvider' => $messages,
                        'itemView' => '_newsList',
                        'layout' => "{items}\n{pager}",
                        'pager' => [
                            'maxButtonCount' => 5,
                        ],
                    ]);
                } catch (Exception $e) {
                    echo Html::tag('div', 'Неудалось отобразить виджет.', ['class' => 'alert alert-danger']);
                }
            ?>
        </div>
        <div class="col-xs-12 col-sm-3">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <?= IconHelper::icon('comments', null, null, 'fa5') ?>
                    <b><?= Yii::t('app', 'Last comments') ?></b>
                </div>
                <div class="panel-body" style="font-size: 12px">
                    <?php if (empty($comments)) { ?>
                        <p class="text-danger"><?= Yii::t('app', 'You have no comments to the classes.') ?></p>
                    <?php } else { ?>
                        <?php foreach ($comments as $comment) { ?>
                            <p>
                                <b><?= Yii::t('app', 'Lesson') ?> <?= date('d.m.Y', strtotime($comment['date'])) ?></b><br/>
                                <i><?= $comment['comments'] ?></i>
                                <?= $comment['successes'] ? join('', Student::prepareStudentSuccessesList((int)$comment['successes'])) : '' ?>
                            </p>
                            <?php } ?>
                        <div class="text-right">
                            <?= Html::a('Далее...', ['student/courses']) ?>
                        </div>
                    <?php } ?>
                </div>
	        </div>
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <?= IconHelper::icon('building', null, null, 'fa5') ?>
                    <b><?= Yii::t('app', 'Our contacts') ?></b>
                </div>
                <div class="panel-body" style="font-size: 12px">
                    <p>
                        <b>Чебоксары:</b>
                    </p>
                    <ul>
                        <li>Московский пр. 17, корп. 1, 1 этаж<br />Тел./факс (8352) 43-96-77</li>
                        <li>пр. Ленина 7, 3 этаж<br />Тел. (8352) 23-02-03</li>
                        <li>пр. 9 Пятилетки 19/37<br />Тел. (8352) 68-50-03</li>
                        <li>ул. Мичмана Павлова, 76<br />Тел. (8352) 68-50-90</li>
                        <li>ул. Н. Смирнова 7<br />Тел. (8352) 68-03-45</li>
                        <li>пр. М. Горького 12<br />Тел. (8352) 68-00-56</li>
                        <li>ул. Строителей 10<br />Тел. (8352) 38-50-61</li>
                    </ul>
                    <p>
                        <b>Новочебоксарск:</b>
                    </p>
                    <ul>
                        <li>ул. Пионерская 4/2<br />Тел. (8352) 68-00-52</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
