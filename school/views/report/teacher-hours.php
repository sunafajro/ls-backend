<?php

use common\widgets\alert\AlertWidget;
use school\widgets\filters\FiltersWidget;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

/**
 * @var yii\web\View $this
 * @var string|null  $end
 * @var array        $data
 * @var array        $reportList
 * @var string|null  $start
 * @var array        $teachers
 * @var string|null  $tid
 * @var string       $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Reports');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Teacher hours');
?>
<div class="row report-teacher-hours">
    <?= $this->render('_sidebar', [
            'actionUrl'     => ['report/teacher-hours'],
            'hints'         => [
                'При установке интервала более недели, отчет будет ограничен выборкой в 7 дней от даты начала периода.'
            ],
            'items'         => [
                [
                    'name'  => 'start',
                    'title' => 'Начало периода',
                    'type'  => FiltersWidget::FIELD_TYPE_DATE_INPUT,
                    'value' => $start ?? '',
                ],
                [
                    'name'  => 'end',
                    'title' => 'Конец периода',
                    'type'  => FiltersWidget::FIELD_TYPE_DATE_INPUT,
                    'value' => $end ?? '',
                ],
                [
                    'name'    => 'tid',
                    'options' => $teachers ?? [],
                    'prompt'  => Yii::t('app', '-all teachers-'),
                    'title'   => 'Преподаватели',
                    'type'    => FiltersWidget::FIELD_TYPE_DROPDOWN,
                    'value'   => $tid ?? '',
                ],
            ],
            'reportList'    => $reportList,

    ]) ?>
    <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') {
            try {
                echo Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
                ]);
            } catch (Exception $e) {
                echo Html::tag('div', 'Не удалось отобразить виджет. ' . $e->getMessage(), ['class' => 'alert alert-danger']);
            }
        } ?>
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>
        <?php
            try {
                echo AlertWidget::widget();
            } catch (Exception $e) {
                echo Html::tag('div', 'Не удалось отобразить виджет. ' . $e->getMessage(), ['class' => 'alert alert-danger']);
            }
        ?>
        <table class="table table-striped table-bordered table-hover table-condensed text-center small">
            <thead>
                <th class="text-center" style="width: 5%">№</th>
                <th class="text-center" style="width: 20%"><?= Yii::t('app', 'Teacher') ?></th>
                <?php foreach (array_keys($data['hours'] ?? []) as $date) { ?>
                    <th class="text-center" style="width: 10%"><?= date('d.m.Y', strtotime($date)) ?></th>
                <?php } ?>
                <th class="text-center" style="width: 5%">Итого</th>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                <?php foreach ($data['teachers'] ?? [] as $id => $name) { ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= Html::a($name, ['teacher/view', 'id' => $id]) ?></td>
                        <?php
                            $totalHours = 0;
                            foreach (array_keys($data['hours'] ?? []) as $date) {
                            $totalHoursByDay = 0;
                            ?>
                            <td>
                                <?php foreach ($data['hours'][$date][$id] ?? [] as $lesson) { ?>
                                    <div>
                                        <?= $lesson['period'] ?> <?= "({$lesson['periodHours']} ч.)" ?>
                                    </div>
                                    <?php
                                        $totalHours += $lesson['periodHours'] ?? 0;
                                        $totalHoursByDay += $lesson['periodHours'] ?? 0;
                                    ?>
                                <?php } ?>
                                <?php if (count($data['hours'][$date][$id] ?? []) > 1) { ?>
                                    <hr style="margin: 0" />
                                    <div class="text-center"><?= $totalHoursByDay ?> ч.</div>
                                <?php } ?>
                            </td>
                        <?php } ?>
                        <td><?= $totalHours ?> ч.</td>
                    </tr>
                    <?php $i++; ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>