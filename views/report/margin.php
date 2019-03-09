<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;
	use yii\widgets\Breadcrumbs;
	$this->title = 'Система учета :: ' . Yii::t('app','Margin report');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
	$this->params['breadcrumbs'][] = Yii::t('app','Margin report');
?>

<div class="row row-offcanvas row-offcanvas-left report-margin">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
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
        <h4><?= Yii::t('app', 'Filters') ?></h4>
        <?php 
            $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['report/margin'],
                ]);
                ?>
            <div class="form-group">
                <select class="form-control input-sm" name="month">
                    <?php 
                    for($m=1; $m < 13; $m++) { ?>												
                        <option value="<?= $m ?>"<?= ($m==$month) ? ' selected' : ''?>>
                            <?= Yii::t('app', date('F', strtotime('1970-' . ($m < 10 ? '0' . $m : $m) . '-01'))) ?>
                        </option>
                    <?php } ?>
                </select>
			</div>
            <div class="form-group">
                <select class="form-control input-sm" name="year">
                    <?php
                    for($y=2011; $y <= date('Y'); $y++) { ?>												
                        <option value="<?= $y ?>"<?= ($y==$year) ? ' selected' : ''?>>
                            <?= $y ?>
                        </option>
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
		<?php if(!empty($teachers)) { ?>
		<table class="table table-hover table-striped table-condensed table-bordered small">
			<thead>
				<tr>
					<th>№</th>
					<th><?= Yii::t('app', 'Teacher') ?></th>
					<th class="text-center"><?= Yii::t('app', 'Lesson count') ?></th>
					<th class="text-center"><?= Yii::t('app', 'Income (w/out discounts)') ?></th>
					<th class="text-center"><?= Yii::t('app', 'Payment') ?></th>
					<th class="text-center"><?= Yii::t('app', 'Margin') ?></th>
					<th class="text-center"><?= Yii::t('app', 'Margin/Income') ?></th>
					<th class="text-center"><?= Yii::t('app', 'Margin/Lesson') ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$i = 1;				
				foreach($teachers as $t) { ?>
				<tr>
					<td><?= $i ?></td>
					<td><?= $t['teacher_name'] ?></td>
					<td class="text-center"><?= $t['lesson_count'] ?></td>
					<td class="text-center"><?= number_format($t['sum_income'], 2, ',', ' ') ?></td>
					<td class="text-center"><?= number_format($t['sum_accrual'], 2, ',', ' ') ?></td>
					<td class="text-center"><?= number_format($t['sum_income'] - $t['sum_accrual'], 2, ',', ' ') ?></td>
					<td class="text-center">
					<?php if($t['sum_income'] > 0) {
						echo round(100 * ($t['sum_income'] - $t['sum_accrual']) / $t['sum_income']);
					} else {
						echo 0;
					} ?>						
					%
					</td>
					<td class="text-center"><?= number_format(($t['sum_income'] - $t['sum_accrual'])/$t['lesson_count'], 2, ',', ' ') ?></td>
				</tr>
				<?php 
				$i++;
				} ?>
			</tbody>
		</table>
		    <?php } else { ?>
            <p class="text-center"><img src="/images/404-not-found.jpg" class="rounded" alt="По вашему запросу ничего не найдено..."></p>
        	<?php } ?>
	</div>
</div>
