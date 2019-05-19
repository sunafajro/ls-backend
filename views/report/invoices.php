<?php
/**
 * @var yii\web\View $this
 * @var array        $dates
 * @var string       $end
 * @var array        $invoices
 * @var array        $offices
 * @var string       $oid
 * @var array        $reportList
 * @var string       $start
 * @var string       $userInfoBlock
 */

use yii\helpers\Html;
use app\widgets\Alert;
use yii\widgets\Breadcrumbs;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Reports');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Invoices report');
?>
<div class="row row-offcanvas row-offcanvas-left report-invoices">
    <?= $this->render('_sidebar', [
        'actionUrl'     => ['report/invoices'],
        'end'           => $end,
        'offices'       => $offices,
        'oid'           => $oid,
        'reportList'    => $reportList,
        'start'         => $start,
        'userInfoBlock' => $userInfoBlock,
    ]) ?>
    <div class="col-sm-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
        ]); ?>
        <?php } ?>
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>
        <?= Alert::widget() ?>
        <?php $totalsum = 0; ?>
        <?php foreach ($dates as $key => $value) { ?>
        <a
          href="#collapse-invoice-<?= $key ?>"
          role="button"
          data-toggle="collapse" aria-expanded="false"
          aria-controls="collapse-invoice-<?= $key ?>"
          class="text-warning">
            <?= date('d.m.y', strtotime($value)) ?> (<?= Yii::t('app', date('l', strtotime($value))) ?>)
        </a>
        <br />
        <div class="collapse" id="collapse-invoice-<?= $key ?>">
        <?php $totaldaysum = 0; ?>
            <table class="table table-bordered table-stripped table-hover table-condensed">
                <tbody>
                <?php foreach ($invoices as $inv) { ?>
                    <?php if ($inv['date'] === $value) { ?>
                        <?php if ((int)$inv['visible'] === 0) { ?>
                            <tr class="danger">
                        <?php } else { ?>
                            <?php if ((int)$inv['done'] === 1) { ?> 
                                <tr class="success">
                            <?php } else { ?>
                                <tr class="warning">
                            <?php } ?>
                        <?php } ?>
                        <td>#<?= $inv['iid'] . ((int)$inv['remain'] === 1 ? ' (ост.)' : '') ?></td>
                        <td><?= $inv['uname'] ?></td>
                        <td><?= Html::a($inv['sname'] . " → ", ['studname/view', 'id' => $inv['sid']]) ?> (усл. #<?= $inv['id'] ?>, <?= $inv['num'] ?> зан.)</td>
                        <td><?= $inv['money'] ?></td>
                        </tr>
                        <?php if ((int)$inv['visible'] === 1 && (int)$inv['remain'] === 0) { ?>
                            <?php $totaldaysum = $totaldaysum + $inv['money']; ?>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <p class="text-right">всего за день: <?= $totaldaysum ?></p>
        <?php $totalsum = $totalsum + $totaldaysum; ?>
        <?php } ?>
        <hr />
        <p class="text-right">всего по офису: <?= $totalsum ?></p>
    </div>
</div>