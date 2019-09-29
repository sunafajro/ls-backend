<?php

/**
 * @var yii\web\View        $this
 * @var yii\data\Pagination $pages
 * @var array               $offices
 * @var string              $oid
 * @var array               $reportlist
 * @var string              $sign
 * @var string              $state
 * @var array               $stds
 * @var array               $students
 * @var float               $totalDebt
 * @var string              $tss
 * @var string              $userInfoBlock
 */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;

$this->title = 'Система учета :: '.Yii::t('app','Debt report');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Debt report');
?>
<div class="row row-offcanvas row-offcanvas-left report-debt">
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
		<h4>Фильтры</h4>
        <?php 
            $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['report/debt'],
                ]);
        ?>
		<div class="form-group">
		    <input type="text" class="form-control input-sm" placeholder="Найти..." name="TSS" value="<?= $tss != '' ? $tss : '' ?>">
		</div>
        <div class="form-group">
	        <select name="STATE" class="form-control input-sm">
		        <option value=""><?php echo Yii::t('app', '-all states-') ?></option>
				<option value="1" <?= $state === '1' ? 'selected' : '' ?>>С нами</option>
				<option value="0" <?= $state === '0' ? 'selected' : '' ?>>Не с нами</option>
	        </select>
        </div>
        <div class="form-group">
	        <select class="form-control input-sm" name="SIGN">
		        <option value=""><?= Yii::t('app', '-all debts-') ?></option>
				<option value="0"<?= $sign === '0' ? 'selected' : '' ?>>Нам должны</option>
                <option value="1"<?= $sign === '1' ? 'selected' : '' ?>>Мы должны</option>
	        </select>
	    </div>
		<?php if ((int)Yii::$app->session->get('user.ustatus') === 3) : ?>
        <div class="form-group">
	        <select name="OID" class="form-control input-sm">
			  <option value=""><?= Yii::t('app', '-all offices-') ?></option>
			  <?php foreach ($offices as $key => $value) : ?>
			      <option value="<?= $key ?>" <?= (int)$oid === $key ? 'selected' : '' ?>><?= $value ?></option>
			  <?php endforeach; ?>
	        </select>
        </div>
		<?php endif; ?>
        <div class="form-group">
            <?= Html::submitButton('<span class="fa fa-filter" aria-hidden="true"></span> ' . Yii::t('app', 'Apply'), ['class' => 'btn btn-info btn-sm btn-block']) ?>
        </div>
        <?php ActiveForm::end(); ?>
	</div>
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

        <?php if ($totalDebt < 0) { ?>
            <div class="alert alert-danger" style="text-align: center">
                <b>
                    Итого: <?= number_format($totalDebt, 2, '.', ' ') ?> р.
                </b>
            </div>
        <?php } ?>

        <?php
			// первый элемент страницы 
			$start = (int)$pages->totalCount > 0 ? 1 : 0;
			// последний элемент страницы
			$end = 20;
			// следующая страница
			$nextpage = 2;
			// предыдущая страница
			$prevpage = 0;
			// проверяем не задан ли номер страницы
			if(Yii::$app->request->get('page')){
				if(Yii::$app->request->get('page')>1){
				// считаем номер первой строки с учетом страницы
					$start = (20 * (Yii::$app->request->get('page') - 1) + 1);
				// считаем номер последней строки с учетом страницы
					$end = $start + 19;
				// если страничка последняя подменяем номер последнего элемента
				if($end>=$pages->totalCount){
					$end = $pages->totalCount;
				}
				// считаем номер следующей страницы
					$prevpage = Yii::$app->request->get('page') - 1;
				// считаем номер предыдущей страницы
					$nextpage = Yii::$app->request->get('page') + 1;
				}
			}
		?>
		<div class="row" style="margin-bottom: 0.5rem">
            <div class="col-xs-12 col-sm-3 text-left">
                <?= (($prevpage > 0) ? Html::a('Предыдущий',['report/debt','page'=>$prevpage,'TSS'=>$tss,'OID'=>$oid,'SIGN'=>$sign,'STATE'=>$state], ['class' => 'btn btn-default']) : '') ?>
            </div>
            <div class="col-xs-12 col-sm-6 text-center">
                <p style="margin-top: 1rem; margin-bottom: 0.5rem">Показано <?= $start ?> - <?= $end >= $pages->totalCount ? $pages->totalCount : $end ?> из <?= $pages->totalCount ?></p>
            </div>
            <div class="col-xs-12 col-sm-3 text-right">
                <?= (($end < $pages->totalCount) ? Html::a('Следующий',['report/debt','page'=>$nextpage,'TSS'=>$tss,'OID'=>$oid,'SIGN'=>$sign,'STATE'=>$state], ['class' => 'btn btn-default']) : '') ?>
            </div>
        </div>
        <?php foreach ($stds as $st) : ?>
	        <div class="<?= $st['debt'] >= 0 ? 'bg-success text-success' : 'bg-danger text-danger' ?>" style="padding: 15px">
                <div style="float: left"><strong><?= Html::a("#".$st['id']." ".$st['name']." →", ['studname/view', 'id'=>$st['id']]) ?></strong></div>
                <div class="text-right"><strong>(баланс: <?= number_format($st['debt'], 2, '.', ' ') ?> р.)</strong></div>
            </div>
            <table class="table table-bordered table-stripped table-hover table-condensed" style="margin-bottom: 0.5rem">
                <tbody>
                <?php foreach ($students as $s) : ?>
    	            <?php if ($s['stid']==$st['id']) : ?>
    		            <tr class="<?= $s['num'] >= 0 ? '' : 'danger'?>">
							<td>услуга #<?= $s['sid'] ?> <?= $s['sname'] ?></td>
							<td class="tbl-cell-10 text-right"><?= $s['num'] ?> зан.</td>
							<td class="tbl-cell-10 text-center">
								<?php if ($s['npd'] !== 'none') : ?>
									<span class="label label-warning" title="Рекомендованная дата оплаты">
										<?= $s['npd'] ?>
									</span>
								<?php else : ?>
									<span class="label label-info">Без расписания</span>
								<?php endif; ?>
							</td>
    		            </tr>
				    <?php endif; ?>
				<?php endforeach; ?>
                </tbody>
            </table>
		<?php endforeach; ?>
		<div class="row" style="margin-bottom: 0.5rem">
            <div class="col-xs-12 col-sm-3 text-left">
                <?= (($prevpage > 0) ? Html::a('Предыдущий',['report/debt','page'=>$prevpage,'TSS'=>$tss,'OID'=>$oid,'SIGN'=>$sign,'STATE'=>$state], ['class' => 'btn btn-default']) : '') ?>
            </div>
            <div class="col-xs-12 col-sm-6 text-center">
                <p style="margin-top: 1rem; margin-bottom: 0.5rem">Показано <?= $start ?> - <?= $end >= $pages->totalCount ? $pages->totalCount : $end ?> из <?= $pages->totalCount ?></p>
            </div>
            <div class="col-xs-12 col-sm-3 text-right">
                <?= (($end < $pages->totalCount) ? Html::a('Следующий',['report/debt','page'=>$nextpage,'TSS'=>$tss,'OID'=>$oid,'SIGN'=>$sign,'STATE'=>$state], ['class' => 'btn btn-default']) : '') ?>
            </div>
        </div>
    </div>
</div>
