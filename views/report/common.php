<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Система учета :: '.Yii::t('app','Common report');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Common report');
?>

<div class="row row-offcanvas row-offcanvas-left report-common">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= $userInfoBlock ?>
        <?php if(!empty($reportlist)): ?>
        <div class="dropdown">
			<?= Html::button('<span class="fa fa-list-alt" aria-hidden="true"></span> ' . Yii::t('app', 'Reports') . ' <span class="caret"></span>', ['class' => 'btn btn-default dropdown-toggle btn-sm btn-block', 'type' => 'button', 'id' => 'dropdownMenu', 'data-toggle' => 'dropdown', 'aria-haspopup' => 'true', 'aria-expanded' => 'true']) ?>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu">
                <?php foreach($reportlist as $key => $value): ?>
                <li><?= Html::a($key, $value, ['class'=>'dropdown-item']) ?></li>
                <?php endforeach; ?>
			</ul>            
		</div>
        <?php endif; ?>
		<h4>Фильтры</h4>
        <?php 
            $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['report/common'],
                ]);
        ?>
        <div class="form-group">
	        <select name="week" class="form-control input-sm">
		        <option value="all"><?php echo Yii::t('app', '-all weeks-') ?></option>
		    	<?php foreach($weeks as $key => $value): ?>
		            <option value="<?= $key ?>" <?php echo ($key==$week) ? ' selected' : ''; ?>>#<?= $value ?></option>
		        <?php endforeach; ?>
	        </select>
        </div>
        <div class="form-group">
	        <select class='form-control input-sm' name='month'>";
		        <option value='all'><?= Yii::t('app', '-all months-') ?></option>";
		    	<?php // распечатываем список месяцев в селект
		        foreach($months as $mkey => $mvalue){ ?>
		            <option value="<?php echo $mkey; ?>" <?php echo ($mkey==$month) ? ' selected' : ''; ?>><?php echo $mvalue; ?></option>
		        <?php } ?>
	        </select>
	    </div>
        <div class="form-group">
	        <select name="year" class="form-control input-sm">
		        <?php
				for ($y=2011; $y<=date('Y'); $y++) { ?>
		            <option value="<?php echo $y; ?>"<?php echo ($year==$y) ? ' selected' : ''; ?>><?php echo $y; ?></option>
		        <?php } ?>
	        </select>
        </div>
        <div class="form-group">
            <?= Html::submitButton('<span class="fa fa-filter" aria-hidden="true"></span> ' . Yii::t('app', 'Apply'), ['class' => 'btn btn-info btn-sm btn-block']) ?>
        </div>
        <?php ActiveForm::end(); ?>
	</div>
	<div class="col-sm-10">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
		<?php if(Yii::$app->session->hasFlash('error')) { ?>
			<div class="alert alert-danger" role="alert">
				<?= Yii::$app->session->getFlash('error') ?>
			</div>
		<?php } ?>

		<?php if(Yii::$app->session->hasFlash('success')) { ?>
			<div class="alert alert-success" role="alert">
				<?= Yii::$app->session->getFlash('success') ?>
			</div>
		<?php } ?>
		<table class='table table-bordered table-stripped table-hover table-condensed'>
		<thead>
			<tr>
			<th>Офис</th>
				<th>Оплаты</th>
				<th>Счета</th>
				<th>Скидки</th>
				<th>Начисления</th>
				<th>Часы</th>
				<th>Студенты</th>
				<th>Долги</th>
			<tr>
		</thead>
		<tbody>
		<?php 
			foreach($common_report as $report) {
		?>
				<tr>
				    <td><small><?= isset($report['name']) ? $report['name'] : '' ?></small></td>
				    <td class="text-right">
					  <small>
					    Нал.: <?= isset($report['payments']) && isset($report['payments']['cash']) ? number_format($report['payments']['cash'], 0, ',', ' ') : 0 ?> р.<br />
					    Терм.: <?= isset($report['payments']) && isset($report['payments']['card']) ? number_format($report['payments']['card'], 0, ',', ' ') : 0 ?> р.<br />
					    Банк.: <?= isset($report['payments']) && isset($report['payments']['bank']) ? number_format($report['payments']['bank'], 0, ',', ' ') : 0 ?> р.<br />
                                            Всего: <?= isset($report['payments']) && isset($report['payments']['money']) ? number_format($report['payments']['money'], 0, ',', ' ') : 0 ?> р.
					  </small>
					</td>
				    <td class="text-right"><small><?= isset($report['invoices']) ? number_format($report['invoices'], 0, ',', ' ') : 0 ?> р.</small></td>
				    <td class="text-right"><small><?= isset($report['discounts']) ? number_format($report['discounts'], 0, ',', ' ') : 0 ?> р.<br />
		            <?php 
		                if($report['oid']==999 && $report['invoices'] > 0) {
		                    echo round(($report['discounts'] * 100) / $report['invoices']) . '% от счетов';
		                }
		            ?>
		            </small></td>
				    <td class="text-right"><small><?= isset($report['accruals']) ? number_format($report['accruals'], 0, ',', ' ') : 0 ?> р.</small></td>
				    <td class="text-right"><small><?= isset($report['hours']) ? number_format($report['hours'], 0, ',', ' ') : 0 ?> час.</small></td>
				    <td class="text-right"><small><?= isset($report['students']) ? number_format($report['students'], 0, ',', ' ') : 0 ?> ч.</small></td>
		            <td class="text-right"><small><?= isset($report['debts']) ? number_format($report['debts'], 0, ',', ' ') : 0 ?> р.</small></td>
		        </tr>
		<?php
			}
		?>

		</tbody>
		</table>
	</div>
</div>
