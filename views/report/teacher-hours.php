<?php

/**
 * @var yii\web\View $this
 * @var string       $end
 * @var array        $data
 * @var array        $reportList
 * @var string       $start
 * @var array        $teachers
 * @var string       $tid
 * @var string       $userInfoBlock
 */

use Yii;
use app\widgets\Alert;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Reports');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Teacher hours');
?>
<div class="row row-offcanvas row-offcanvas-left report-teacher-hours">
    <?= $this->render('_sidebar', [
        'actionUrl'     => ['report/teacher-hours'],
        'end'           => $end,
        'reportList'    => $reportList,
        'teachers'      => $teachers,
        'tid'           => $tid,
        'start'         => $start,
        'userInfoBlock' => $userInfoBlock,
        'hints'         => [
            'При установке интервала более недели, отчет будет ограничен выборкой в 7 дней от даты начала периода.'
        ],
    ]) ?>
    <div class="col-xs-12 col-sm-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
        ]); ?>
        <?php } ?>
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>
        <?= Alert::widget() ?>
        <table class="table table-striped table-bordered table-hover table-condensed small">
            <thead>
                <th style="width: 5%">№</th>
                <th style="width: 25%"><?= Yii::t('app', 'Teacher') ?></th>
                <?php foreach (array_keys($data['hours'] ?? []) as $date) { ?>
                    <th style="width: 10%"><?= date('d.m.Y', strtotime($date)) ?></th>
                <?php } ?>
                <th>Итого, ч</th>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                <?php foreach ($data['teachers'] ?? [] as $id => $name) { ?>
                    <tr>
                        <td style="width: 5%"><?= $i ?></td>
                        <td style="max-width: 20%"><?= Html::a($name, ['teacher/view', 'id' => $id]) ?></td>
                        <?php
                            $totalHours = 0;
                            foreach (array_keys($data['hours'] ?? []) as $date) {
                            $totalHoursByDay = 0;
                            ?>
                            <td style="width: 10%">
                                <?php foreach ($data['hours'][$date][$id] ?? [] as $lesson) { ?>
                                    <div>
                                        <?= $lesson['period'] ?> <?= "({$lesson['periodHours']})" ?>
                                    </div>
                                    <?php
                                        $totalHours += $lesson['periodHours'] ?? 0;
                                        $totalHoursByDay += $lesson['periodHours'] ?? 0;
                                    ?>
                                <?php } ?>
                                <?php if (count($data['hours'][$date][$id] ?? []) > 1) { ?>
                                    <hr style="margin: 0" />
                                    <div class="text-center"><?= $totalHoursByDay ?></div>
                                <?php } ?>
                            </td>
                        <?php } ?>
                        <td style="width: 5%"><?= $totalHours ?></td>
                    </tr>
                    <?php $i++; ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>