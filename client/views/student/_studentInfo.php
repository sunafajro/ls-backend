<?php

/**
 * @var View $this
 * @var float $balance
 * @var Student $student
 */

use client\models\Student;
use yii\helpers\Html;
use yii\web\View;

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <b><?= Yii::t('app', 'Information') ?></b>
    </div>
    <div class="panel-body" style="font-size: 16px">
        <p>
            <?= Html::tag(
                'i',
                '',
                [
                    'class' => 'fas fa-user',
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'left',
                    'aria-hidden' => 'true',
                    'title' => Yii::t('app', 'Full name')
                ]
            ) ?> <?= $student->name ?>
        </p>
        <?php if ($student->phone) { ?>
            <p>
                <?= Html::tag(
                    'i',
                    '',
                    [
                        'class' => 'fas fa-mobile-alt',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'left',
                        'aria-hidden' => 'true',
                        'title' => Yii::t('app', 'Phone')
                    ]
                ) ?> <?= $student->phone ?>
            </p>
        <?php } ?>
        <?php if ($student->email) { ?>
            <p>
                <?= Html::tag(
                    'i',
                    '',
                    [
                        'class' => 'fas fa-envelope',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'left',
                        'aria-hidden' => 'true',
                        'title' => Yii::t('app', 'E-mail')
                    ]
                ) ?> <?= $student->email ?>
            </p>
        <?php } ?>
        <p>
            <?= Html::tag(
                    'i',
                    '',
                    [
                        'class' => 'fas fa-ruble-sign',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'left',
                        'aria-hidden' => 'true',
                        'title' => Yii::t('app', 'Balance')
                    ]
                ) ?> <?= $balance >= 0 ? Html::tag('b', $balance, ['class' => 'text-success']) : Html::tag('b', $balance, ['class' => 'text-danger']) ?>
        </p>
        <p>
            <?php
            $successesCount = $student->getSuccessesCount();
            echo Html::tag(
                'i',
                '',
                [
                    'class' => 'fas fa-ticket-alt',
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'left',
                    'aria-hidden' => 'true',
                    'title' => 'Баланс успешиков',
                ]) . ' ' . $successesCount;
            ?>
        </p>
    </div>
</div>