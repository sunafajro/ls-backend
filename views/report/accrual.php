<?php

use app\widgets\Alert;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;

/**
 * @var View   $this
 * @var array  $accruals
 * @var array  $groups
 * @var array  $jobPlaces
 * @var array  $lessons
 * @var array  $months
 * @var int    $pages
 * @var array  $params
 * @var array  $reportlist
 * @var array  $teachers
 * @var array  $teachers_list
 * @var string $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . 'Отчет по начислениям';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = 'Отчет по начислениям';
?>
<div class="row row-offcanvas row-offcanvas-left schedule-index">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
        <?= $userInfoBlock ?>
        <?php if (!empty($reportlist)) { ?>
        <div class="dropdown">
            <?= Html::button('<span class="fa fa-list-alt" aria-hidden="true"></span> ' . Yii::t('app', 'Reports') . ' <span class="caret"></span>', ['class' => 'btn btn-default dropdown-toggle btn-sm btn-block', 'type' => 'button', 'id' => 'dropdownMenu', 'data-toggle' => 'dropdown', 'aria-haspopup' => 'true', 'aria-expanded' => 'true']) ?>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu">
                <?php foreach ($reportlist as $key => $value) { ?>
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
                    <?php if (!empty($teachers_list)) {
                        foreach ($teachers_list as $key => $value) { ?>
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
        <?= Alert::widget() ?>
        <?php if (!$params['tid'] || $params['tid'] == 'all') {
            $current = 1;
            $start = 1;
            $end = 10;
            $prevpage = 0;
            $nextpage = 2;
            if (Yii::$app->request->get('page')) {
                $current = (int)Yii::$app->request->get('page');
                $start = 10 * (int)Yii::$app->request->get('page') - 9;
                $end = 10 * (int)Yii::$app->request->get('page');
                if ($end>$pages) {
                    $end = $pages;
                }
                $prevpage = (int)Yii::$app->request->get('page') - 1;
                $nextpage = (int)Yii::$app->request->get('page') + 1;
            }
            ?>
            <nav>
                <ul class="pager">
                    <li class="previous">
                        <?= (($start > 1) ? Html::a('Предыдущий', ['report/accrual', 'page' => $prevpage, 'tid' => $params['tid'], 'month' => $params['month']]) : '') ?>
                    </li>
                    <li class="next">
                        <?= (($end < $pages) ? Html::a('Следующий', ['report/accrual', 'page' => $nextpage, 'tid' => $params['tid'], 'month' => $params['month']]) : '') ?>
                    </li>
                </ul>
            </nav>
            <?php $page = $nextpage - 1;
        } else {
            $page = 0;
        }

        // задаем общую сумму по начислениям
        $totalAccural = 0;
        $totalPayment = 0;
	?>
	<?php foreach($teachers as $teacher): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= Html::a(
                        $teacher['name'],
                        ['teacher/view', 'id' => $teacher['id']],
                        ['id'=> 'block_tid_' . $teacher['id']]
                    ) ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ставка: <?= implode(' р. / ', $teacher['value']) ?> р.
            </div>
            <div class="panel-body">
                <?php
                    $time = 0;
                    $money = 0;
                ?>
                <?php foreach($groups as $groupteacher): ?>
                    <?php foreach($groupteacher as $group): ?>
                        <?php if((int)$teacher['id'] === (int)$group['tid']): ?>
                        <div>
                            <div class="clearfix" style="margin-bottom: 5px">
                                <a class="pull-left" role="button" data-toggle="collapse" href="#collapse-<?= $group['gid']?>-<?= $teacher['id']?>" aria-expanded="false" aria-controls="collapse-<?= $group['gid']?>-<?= $teacher['id']?>">
                                    <span style="margin-top: 2px" class="label <?= ((int)$group['tjplace'] === 1 ? 'label-success' : 'label-info') ?> pull-left"><?= $jobPlaces[$group['tjplace']] ?></span>&nbsp;
                                    #<?= $group['gid'] ?> <?= $group['course'] ?>, ур. <?= $group['level'] ?> (усл.#<?= $group['service'] ?>), <?= $group['office'] ?>
                                </a>
                                <?= Html::a(
                                        "Начислить {$group['time']} ч.",
                                        ['accrual/add-accrual', 'gid' => $group['gid'], 'tid' => $teacher['id'], 'month' => $params['month'] ?? null],
                                        ['class' => 'btn btn-xs btn-success pull-right']
                                    ) ?>
                            </div>
                            <table class="table table-condensed collapse" id="collapse-<?= $group['gid']?>-<?= $teacher['id']?>">
						<?php foreach($lessons as $lesson): ?>
							<?php if($lesson['tid']==$group['tid'] && $lesson['gid']==$group['gid']): ?>
								<tr>
									<td width="tbl-cell-5">
									<?php switch($lesson['edutime']) {
										case 1: echo Html::img('@web/images/day.png');break;
										case 2: echo Html::img('@web/images/night.png');break;
										case 3: echo Html::img('@web/images/halfday.png');break;
									} ?>
									</td>
									<td width="tbl-cell-10">#<?= $lesson['jid'] ?></td>
									<td class="tbl-cell-5"><?= ($lesson['view'] ? '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>' : '') ?></td>
									<td class="tbl-cell-10"><?= Html::a($lesson['jdate'],['groupteacher/view','id'=>$group['gid']]) ?></td>
									<td class="tbl-cell-5"><?= $lesson['pcount'] ?> чел.</td>
									<td><?= $lesson['desc'] ?></td>
									<td class="text-right tbl-cell-5"><?= $lesson['time'] ?> ч.</td>
									<td class="text-right tbl-cell-5"><?= $lesson['money'] ?> р.</td>
								</tr>
								<?php
									$time += $lesson['time'];
									$money += $lesson['money'];
								?>
							<?php endif; ?>
						<?php endforeach; ?>
					</table>
					</div>
		        <?php endif; ?>
                    <?php endforeach; ?>
		<?php endforeach; ?>
            <?php $sum = 0; ?>
		    <?php if(!empty($accruals)) : ?>
                <?php foreach($accruals as $a): ?>
                    <?php if($a['tid']==$teacher['id']): ?>
                        <p>начисление зарплаты #<?= $a['aid'] ?> (за <?= $a['hours'] ?> ч. в группе #<?= Html::a($a['gid'], ['groupteacher/view', 'id'=>$a['gid']]) ?>) на сумму <span class="text-danger"><?= number_format($a['value'], 2, ',', ' ') ?></span> р. <?= Html::a('Выплатить', ['accrual/doneaccrual', 'id' => $a['aid'], 'type' => 'report', 'TID'=>$teacher['id'], 'page' => $page], ['class' => 'btn btn-warning btn-xs pull-right']) ?></p>
                        <?php $sum = $sum + $a['value']; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            <p class="text-right text-muted">всего к начислению за <?= isset($time) ? $time : 0 ?> ч. : <strong><?= isset($money) ? number_format($money, 2, ',', ' ') : 0 ?></strong> р.
            <br />всего к выплате: <strong><?= isset($sum) ? number_format($sum, 2, ',', ' ') : 0 ?></strong> р.</p>
			</div><!-- panel-body-->
	    </div><!-- panel -->
	    <?php 
                $totalAccural += $money;
                $totalPayment += $sum; 
            ?>
    <?php endforeach ?>
    <?php if($totalAccural != 0 && $totalPayment != 0): ?>
    <p class="text-right">всего к начислению (без надбавок): <strong><?= number_format($totalAccural, 2, ',', ' ') ?> р.</strong><br/>
    всего к выплате: <strong><?= number_format($totalPayment, 2, ',', ' ') ?></strong> р.</p>
    <?php endif ?>
    <?php if(!$params['tid'] || $params['tid'] == 'all') : ?>
		<nav>
		    <ul class="pager">
		        <li class="previous"><?= (($start>1) ? Html::a('Предыдущий', ['report/accrual', 'page' => $prevpage, 'tid' => $params['tid'], 'month' => $params['month']]) : '') ?></li>
		        <li class="next"><?= (($end<$pages) ? Html::a('Следующий', ['report/accrual', 'page' => $nextpage, 'tid' => $params['tid'], 'month' => $params['month']]) : '') ?></li>
		    </ul>
		</nav>
    <?php endif ?>
	</div>
</div>
