<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Система учета :: '.Yii::t('app','Reports');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => 'report/index'];
$this->params['breadcrumbs'][] = Yii::t('app','Plan for office');
?>

<div class="row row-offcanvas row-offcanvas-left report-plan">
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
		<h4><?= Yii::t('app', 'Actions') ?>:</h4>
		<?php $form = ActiveForm::begin([
				'method' => 'get',
				'action' => ['report/plan'],
			]); 
		?>
		<div class="form-group form-group-sm">
			<div class="checkbox">
				<label>
				<input type="checkbox" name="next"<?= $next ? 'checked' : ''; ?> title="<?= Yii::t('app', 'Next month'); ?>">
				<?= Yii::t('app', 'Next month'); ?>
				</label>
			</div>
		</div>
		<div class="form-group form-group-sm">
			<select class='form-control' name='oid'>
			<?php foreach($offices as $key => $value) { ?>
				<option value="<?php echo $key; ?>"<?php echo ($oid == $key) ? 'selected' : ''; ?>><?php echo $value; ?></option>
			<?php } ?>
			</select>
		</div>
		<div class="form-group form-group-sm">
            <?= Html::submitButton('<span class="fa fa-filter" aria-hidden="true"></span> ' . Yii::t('app', 'Apply'), ['class' => 'btn btn-info btn-sm btn-block']) ?>
		</div>

		<?php ActiveForm::end(); ?>

		<p><b>Занятий по расписанию:</b><br> <?php echo $lessonplan; ?> шт.</p>
		<p><b>Оплат по занятиям:</b><br> <?php echo number_format($moneyplan, 2, ',', ' '); ?> р.</p>

    </div>
    <div id="content" class="col-sm-10">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
		<?php if(Yii::$app->session->hasFlash('error')): ?>
			<div class="alert alert-danger" role="alert">
				<?= Yii::$app->session->getFlash('error') ?>
			</div>
		<?php endif; ?>

		<?php if(Yii::$app->session->hasFlash('success')): ?>
			<div class="alert alert-success" role="alert">
				<?= Yii::$app->session->getFlash('success') ?>
			</div>
		<?php endif; ?>

        <h4>План на <?php echo Yii::t('app', $monthname); ?> по офису <?php echo $office->name; ?></h4>

        <table class="table table-hover table-bordered table-striped table-condensed text-center small">
			<thead>
				<tr>
					<th>Группа</th>
					<th>Стоимость, р.</th>
					<th>День недели</th>
					<th>Колич. занятий, шт.</th>
					<th>Колич. учеников, ч.</th>
					<th>Всего занятий, шт.</th>
					<th>Итого, р.</th>
				</tr>
			</thead>
			<?php foreach($grouplist as $gl) { ?>
			<tr>
				<td><?= $gl['group']; ?></td>
				<td><?= $gl['cost']; ?></td>
				<td><?= $daynames[$gl['day']]; ?></td>
				<td><?= $gl['cnt']; ?></td>
				<td><?= $gl['pupils']; ?></td>
				<td><?= $gl['totalcnt']; ?></td>
				<td><?= number_format($gl['totalcost'], 2, ',', ' '); ?></td>
			</tr>
			<?php } ?>
		</table>
	</div>
</div>