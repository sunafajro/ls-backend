<?php

/**
 * @var View $this
 * @var int $month
 * @var int $year
 */

use app\components\helpers\DateHelper;
use app\widgets\alert\AlertWidget;
use app\widgets\userInfo\UserInfoWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->title = 'Система учета :: ' . Yii::t('app','Margin report');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Margin report');
?>
<div class="row report-margin">
    <div id="sidebar" class="col-xs-12 col-sm-12 col-md-2 col-lg-2 col-xl-2">
        <?= UserInfoWidget::widget() ?>
        <?php if (!empty($reportlist)) { ?>
            <div class="dropdown">
                <?= Html::button('<span class="fa fa-list-alt" aria-hidden="true"></span> ' . Yii::t('app', 'Reports') . ' <span class="caret"></span>', ['class' => 'btn btn-default dropdown-toggle btn-sm btn-block', 'type' => 'button', 'id' => 'dropdownMenu', 'data-toggle' => 'dropdown', 'aria-haspopup' => 'true', 'aria-expanded' => 'true']) ?>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu">
                    <?php foreach($reportlist as $key => $value): ?>
                    <li><?= Html::a($key, $value, ['class'=>'dropdown-item']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php } ?>
        <h4><?= Yii::t('app', 'Filters') ?>:</h4>
        <?php 
            $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['report/margin'],
                ]);
                ?>
            <div class="form-group">
                <select class="form-control input-sm" name="month">
                    <?php 
                    foreach (DateHelper::getMonths() ?? [] as $key => $value) { ?>
                        <option value="<?= $key ?>"<?= ($key==$month) ? ' selected' : ''?>>
                            <?= $value ?>
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
    <div id="content" class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?= AlertWidget::widget() ?>
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
