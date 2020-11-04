<?php

/**
 * @var View       $this
 * @var ActiveForm $form
 * @var array      $accruals
 * @var array      $groups
 * @var array      $jobPlaces
 * @var array      $lessons
 * @var array      $months
 * @var int        $pages
 * @var array      $params
 * @var array      $reportList
 * @var array      $teachers
 * @var array      $teachersList
 * @var string     $userInfoBlock
 */

use app\modules\school\assets\ReportAccrualsAsset;
use app\widgets\alert\AlertWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;

$this->title = Yii::$app->params['appTitle'] . 'Отчет по начислениям';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = 'Отчет по начислениям';

ReportAccrualsAsset::register($this);
?>
<div class="row row-offcanvas row-offcanvas-left schedule-index">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?>
        <?php if (!empty($reportList)) { ?>
        <div class="dropdown">
            <?= Html::button(
                    Html::tag('span', '', ['class' => 'fa fa-list-alt', 'aria-hidden' => 'true']) 
                    . ' '
                    . Yii::t('app', 'Reports')
                    . ' '
                    . Html::tag('span', '', ['class' => 'caret']),
                    [
                        'class'         => 'btn btn-default dropdown-toggle btn-sm btn-block',
                        'type'          => 'button',
                        'id'            => 'dropdownMenu',
                        'data-toggle'   => 'dropdown',
                        'aria-haspopup' => 'true',
                        'aria-expanded' => 'true'
                    ]
                ) ?>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu">
                <?php foreach ($reportList as $key => $value) { ?>
                <li><?= Html::a($key, $value, ['class'=>'dropdown-item']) ?></li>
                <?php } ?>
            </ul>
        </div>
        <?php } ?>
        <h4><?= Yii::t('app', 'Filters') ?></h4>
        <?php $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['report/accrual'],
                ]); ?>
            <div class="form-group">
                <select class='form-control input-sm' name='month'>";
                    <option value='all'><?= Yii::t('app', '-all months-') ?></option>";
                    <?php foreach ($months as $mkey => $mvalue) { ?>
                        <option value="<?= $mkey ?>" <?php echo ($mkey==$params['month']) ? ' selected' : ''; ?>>
                            <?= $mvalue ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <select class="form-control input-sm" name="tid">
                    <option value="all"><?= Yii::t('app', '-all teachers-') ?></option>
                    <?php if (!empty($teachersList)) {
                        foreach ($teachersList as $key => $value) { ?>
                            <option value="<?= $key ?>"<?= ($key == $params['tid']) ? ' selected' : ''?>>
                                <?= $value ?>
                            </option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <?= Html::submitButton('<span class="fa fa-filter" aria-hidden="true"></span> ' . Yii::t('app', 'Apply'), ['class' => 'btn btn-info btn-sm btn-block']) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div id="content" class="col-sm-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
        ]); ?>
        <?php endif; ?>
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>
        <?= AlertWidget::widget() ?>
        <?php
            $pager = []; 
            if (!$params['tid'] || $params['tid'] == 'all') {
                $current = 1;
                $start = 1;
                $end = 10;
                $prevpage = 0;
                $nextpage = 2;
                if ($params['page']) {
                    $current = (int)$params['page'];
                    $start   = 10 * (int)$params['page'] - 9;
                    $end     = 10 * (int)$params['page'];
                    if ($end > $pages) {
                        $end = $pages;
                    }
                    $prevpage = (int)$params['page'] - 1;
                    $nextpage = (int)$params['page'] + 1;
                }
                $pager[] = Html::beginTag('nav');
                $pager[] = Html::beginTag('ul', ['class' => 'pager']);
                $pager[] = Html::tag(
                    'li',
                    (($start > 1) ? Html::a('Предыдущий', ['report/accrual', 'page' => $prevpage, 'tid' => $params['tid'], 'month' => $params['month']]) : ''),
                    ['class' => 'previous']
                );
                $pager[] = Html::tag(
                    'li',
                    (($end < $pages) ? Html::a('Следующий', ['report/accrual', 'page' => $nextpage, 'tid' => $params['tid'], 'month' => $params['month']]) : ''),
                    ['class' => 'next']
                );
                $pager[] = Html::endTag('ul');
                $pager[] = Html::endTag('nav');
                $page = $nextpage - 1;
        } else {
            $page = 0;
        }

        // задаем общую сумму по начислениям
        $totalAccrual = 0;
        $totalPayment = 0;

        echo join('', $pager);
	?>
	<?php foreach($teachers as $teacher): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= Html::a(
                        $teacher['name'],
                        ['teacher/view', 'id' => $teacher['id']],
                        [
                            'id'    => 'block_tid_' . $teacher['id'],
                            'style' => 'margin-right: 10px',
                        ]
                    ) ?>
                <span>ставка: <?= implode(' р. / ', $teacher['value']) ?> р.</span>
                <?= Html::a(
                        'Начислить все',
                        ['accrual/create', 'tid' => $teacher['id'], 'month' => $params['month'] ?? null],
                        [
                            'class' => 'btn btn-xs btn-success pull-right js--accrual-all-link',
                            'data' => [
                                'method' => 'POST',
                                'params' => '',
                            ],
                            'style' => 'display: none',
                        ]
                    ) ?>
            </div>
            <div class="panel-body">
                <?php
                    $time = 0;
                    $money = 0;
                ?>
                <?php foreach ($groups as $groupId => $groupTeacher) { ?>
                    <?php foreach ($groupTeacher as $group) { ?>
                        <?php if ((int)$teacher['id'] === (int)$group['teacherId']) { ?>
                            <div>
                                <div class="clearfix" style="margin-bottom: 5px">
                                    <a class="pull-left" role="button" data-toggle="collapse" href="#collapse-<?= $groupId ?>-<?= $teacher['id']?>" aria-expanded="false" aria-controls="collapse-<?= $groupId ?>-<?= $teacher['id']?>">
                                        <span style="margin-top: 2px" class="label <?= ((int)$group['company'] === 1 ? 'label-success' : 'label-info') ?> pull-left"><?= $jobPlaces[$group['company']] ?></span>&nbsp;
                                        #<?= $groupId ?> <?= $group['service'] ?>, ур. <?= $group['level'] ?> (усл.#<?= $group['serviceId'] ?>), <?= $group['office'] ?>
                                    </a>
                                    <?= Html::a(
                                            "Начислить {$group['time']} ч.",
                                            ['accrual/create', 'tid' => $teacher['id'], 'month' => $params['month'] ?? null],
                                            [
                                                'class' => 'btn btn-xs btn-success pull-right js--accrual-link',
                                                'data' => [
                                                    'method' => 'post',
                                                    'params' => [
                                                        'groups' => [$groupId],
                                                    ],
                                                    'group-id' => $groupId,
                                                ],
                                            ]
                                        ) ?>
                                </div>
                                <table class="table table-condensed collapse" id="collapse-<?= $groupId ?>-<?= $teacher['id']?>">
                                <?php foreach ($lessons as $lesson) { ?>
                                    <?php if ($lesson['teacherId'] == $group['teacherId'] && $lesson['groupId'] == $groupId) { ?>
                                        <tr>
                                            <td width="tbl-cell-5">
                                            <?php switch ($lesson['eduTimeId']) {
                                                case 1: echo Html::img('@web/images/day.png'); break;
                                                case 2: echo Html::img('@web/images/night.png'); break;
                                                case 3: echo Html::img('@web/images/halfday.png'); break;
                                            } ?>
                                            </td>
                                            <td width="tbl-cell-10">#<?= $lesson['id'] ?></td>
                                            <td class="tbl-cell-5"><?= ($lesson['viewed'] ? '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>' : '') ?></td>
                                            <td class="tbl-cell-10"><?= Html::a(date('d.m.Y', strtotime($lesson['date'])), ['groupteacher/view','id' => $groupId]) ?></td>
                                            <td class="tbl-cell-5"><?= $lesson['studentCount'] ?> чел.</td>
                                            <td><?= $lesson['description'] ?></td>
                                            <td class="text-right tbl-cell-5"><?= $lesson['time'] ?> ч.</td>
                                            <td class="text-right tbl-cell-5"><?= $lesson['money'] ?> р.</td>
                                        </tr>
                                        <?php
                                            $time += $lesson['time'];
                                            $money += $lesson['money'];
                                        ?>
                                    <?php } ?>
                                <?php } ?>
                                </table>
                            </div>
		                <?php } ?>
                    <?php } ?>
		        <?php } ?>
            <?php $sum = 0; ?>
		    <?php if (!empty($accruals)) { ?>
                <?php foreach ($accruals as $a) { ?>
                    <?php if ($a['tid']==$teacher['id']) { ?>
                        <p>начисление зарплаты #<?= $a['aid'] ?> (за <?= $a['hours'] ?> ч. в группе #<?= Html::a($a['gid'], ['groupteacher/view', 'id'=>$a['gid']]) ?>) от <?= date('d.m.Y', strtotime($a['date'])) ?> на сумму <span class="text-danger"><?= number_format($a['value'], 2, ',', ' ') ?></span> р. <?= Html::a('Выплатить', ['accrual/done', 'id' => $a['aid'], 'type' => 'report', 'TID'=>$teacher['id'], 'page' => $page], ['class' => 'btn btn-warning btn-xs pull-right', 'data-method' => 'post']) ?></p>
                        <?php $sum = $sum + $a['value']; ?>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
            <p class="text-right text-muted">
                всего к начислению за <?= isset($time) ? $time : 0 ?> ч. : <strong><?= isset($money) ? number_format($money, 2, ',', ' ') : 0 ?></strong> р.
                <br />всего к выплате: <strong><?= isset($sum) ? number_format($sum, 2, ',', ' ') : 0 ?></strong> р.
            </p>
			</div><!-- panel-body-->
	    </div><!-- panel -->
	    <?php 
            $totalAccrual += $money;
            $totalPayment += $sum; 
        ?>
    <?php endforeach ?>
    <?php if ($totalAccrual != 0 && $totalPayment != 0) { ?>
        <p class="text-right">всего к начислению (без надбавок): <b><?= number_format($totalAccrual, 2, ',', ' ') ?> р.</b>
        <br/>всего к выплате: <b><?= number_format($totalPayment, 2, ',', ' ') ?></b> р.</p>
        <?php } ?>
        <?= join('', $pager) ?>
	</div>
</div>
