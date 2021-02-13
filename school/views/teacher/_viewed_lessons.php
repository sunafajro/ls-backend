<?php
/**
 * @var View  $this
 * @var array $viewedLessons
 */
use yii\helpers\Html;
use yii\web\View;
?>
<a href="#collapse-accruals" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="collapse-accruals" class="text-warning">
    показать/скрыть занятия для начисления (<?= count($viewedLessons) ?>)
</a>
<div class="collapse" id="collapse-accruals">
    <?php
        if (!empty($viewedLessons)) {
            foreach ($viewedLessons as $viewed) { ?>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?php
                            switch ($viewed['eduTimeId']) {
                                case 1:
                                    echo Html::img('@web/images/day.png', ['alt'=>'Дневное время', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Дневное: - 50 руб от ставки']);
                                    break;
                                case 2:
                                    echo Html::img('@web/images/night.png', ['alt'=>'Вечернее время', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Вечернее: полная ставка']);
                                    break;
                                case 3:
                                    // @deprecated
                                    echo Html::img('@web/images/halfday.png', ['alt'=>'Полурабочее время']);
                                    break;
                            } ?>
                        <small>
                            <span class="inblocktext">
                                <?= Html::a("Занятие #{$viewed['id']} в группе #{$viewed['groupId']}", ['groupteacher/view', 'id' => $viewed['groupId'], '#' => "lesson_{$viewed['id']}"]);?>
                                <br />
                                <?= join(', ', [
                                        $viewed['service'],
                                        Html::tag(
                                            'span',
                                           "ур: {$viewed['level']}",
                                            [
                                                'data-toggle' => 'tooltip',
                                                'data-placement' => 'top',
                                                'style' => 'text-decoration: underline; cursor: pointer',
                                                'title' => "коэф. {$viewed['groupLevelRate']}"
                                            ]
                                        ),
                                        "прод.: {$viewed['time']} ч.",
                                        Html::tag(
                                                'span',
                                                "кол.чел.: {$viewed['studentCount']}",
                                                [
                                                    'data-toggle' => 'tooltip',
                                                    'data-placement' => 'top',
                                                    'style' => 'text-decoration: underline; cursor: pointer',
                                                    'title' => "коэф. {$viewed['studentCountRate']}"
                                                ]
                                        ),
                                    ]) ?>
                                <br />
                                <?= date('d.m.y', strtotime($viewed['date'])) ?> (<?= Yii::t('app', date('l', strtotime($viewed['date']))) ?>),
                                <?= $viewed['office'] ?>,
                                к начислению: <?= $viewed['totalValue']?> р. <i>(ставка <?= $viewed['wageRate'] ?> р. <?= ($viewed['corp'] > 0 ? (' + ' . $viewed['corpPremium'] . 'р.') : '') ?> )</i></span>
                        </small>
                    </div>
                </div>
        <?php } ?>
    <?php } ?>
</div>