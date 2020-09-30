<?php

use yii\helpers\Html;
use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;
use yii\widgets\Menu;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Expenses');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="kaslibro-index">
<?php
    // формируем список ссылок меню
    $items[] = ['label' => \Yii::t('app','Add'), 'url' => ['create']];
	if(\Yii::$app->session->get('user.ustatus')==3) {
		$items[] = ['label' => \Yii::t('app', 'References'), 'url' => '#', 'items'=> [
			['label' => \Yii::t('app', 'Operation sense'), 'url' => ['kaslibrooperation/index']],
			['label' => \Yii::t('app', 'Client'), 'url' => ['kaslibroclient/index']],
			['label' => \Yii::t('app', 'Executor'), 'url' => ['kaslibroexecutor/index']],
			['label' => \Yii::t('app', 'Office'), 'url' => ['kaslibrooffice/index']],
			['label' => \Yii::t('app', 'Code'), 'url' => ['kaslibrocode/index']],
		]];
	}
    // формируем список ссылок меню
	
    NavBar::begin([
        'renderInnerContainer' => false,
    ]);
	
	// выводим меню
	echo Nav::widget([
		'options' => ['class' => 'navbar-nav navbar-left'],
		'items' => $items,
	]);
	// выводим меню
	
	$form = ActiveForm::begin([
        'method' => 'get',
        'options' => ['class' => 'navbar-form navbar-right'],
        'action' => 'index.php?r=kaslibro/index',
    ]);
?>
    <div class="form-group">
    <select class='form-control input-sm' name='month'>
    <option value='all'><?php echo \Yii::t('app', '-all months-') ?></option>
    <?php
    // распечатываем список месяцев в селект
    foreach($months as $mkey => $mname){ ?>
        <option value="<?php echo $mkey; ?>"<?php echo ($mkey==$month) ? ' selected' : ' '; ?>><?php echo $mname; ?></option>
    <?php }
	// распечатываем список месяцев в селект
	?>
    </select>
    </div>
	<?php if(\Yii::$app->session->get('user.ustatus') != 4) { ?>
    <div class="form-group">
	<select class='form-control input-sm' name='office'>
    <option value='all'><?php echo \Yii::t('app', '-all offices-') ?></option>
    <?php
    // распечатываем список месяцев в селект
    foreach($offices as $o){ ?>
        <option value="<?php echo $o['id']; ?>"<?php echo ($o['id']==$office) ? ' selected' : ' '; ?>><?php echo $o['name']; ?></option>
    <?php }
	// распечатываем список месяцев в селект
	?>
	</select>
	</div>
	<?php } ?>
    <button type="submit" class="btn btn-default btn-sm glyphicon glyphicon-filter"></button>
<?php
    ActiveForm::end();
    NavBar::end();
?>
    <?php 
	// сообщение об ошибке при добавлении
    if(Yii::$app->session->hasFlash('error')) { ?>
        <div class="alert alert-danger" role="alert">
        <?php echo Yii::$app->session->getFlash('error'); ?>
        </div>
    <?php }
    // сообщение об успешном добавлении     
    if(Yii::$app->session->hasFlash('success')) { ?>
        <div class="alert alert-success" role="alert">
        <?php echo \Yii::$app->session->getFlash('success'); ?>
        </div>
    <?php } ?>
	
    <table class="table table-bordered table-stripped table-hover small text-center">
        <thead>
            <tr>
                <th class="text-center">№</th>
                <th class="text-center">Дата</th>
                <th class="text-center">Суть операции</th>
                <th class="text-center">Детали операции</th>
                <th class="text-center">Агент/Клиент</th>
                <th class="text-center">Исполнитель</th>
                <th class="text-center">Месяц</th>
                <th class="text-center">Офис</th>
                <th class="text-center">Код</th>
				<?php
				/*
				if(\Yii::$app->session->get('user.ustatus')==3) { ?>
                <th class="text-center warning" width="5%">АВ +</th>
                <th class="text-center info" width="5%">Банк+</th>
                <?php } */ ?>
                <th class="text-center success" width="7%">Нал +</th>
				<?php
				/*
				if(\Yii::$app->session->get('user.ustatus')==3) { ?>
					<th class="text-center warning" width="5%">АВ -</th>
					<th class="text-center info" width="5%">Банк -</th>
				<?php }
				*/ ?>
                <th class="text-center success" width="7%">Нал -</th>
				<?php
				/* if(\Yii::$app->session->get('user.ustatus')==3) { ?>
					<th class="text-center warning" width="5%">АВ ∑</th>
					<th class="text-center info" width="6%">Банк ∑</th>
				<?php } */ ?>
                <th class="text-center success" width="7%">Нал ∑</th>
				<th class="text-center"><span class="glyphicon glyphicon-wrench"></span></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
			$total_sum = 0.0;
            //$av_sum = $sum['av_sum'];
            //$b_sum = $sum['b_sum'];
			$n_minus = 0;
			$n_plus = 0;
            $n_sum = 0;
			//$sum['n_sum'];
            foreach($model as $m) {
				$color = NULL;
				if($m['reviewed']==0) { $color = ' class="warning"';}
				if($m['reviewed']==1 && $m['done']==0) { $color = ' class="info"';}
            ?>
            <tr<?php echo $color ? $color : ''; ?>>
                <td><?php echo $i; ?></td>
                <td><?php echo date('d.m.y', strtotime($m['date'])); ?></td>
                <td><?php echo $m['operation']; ?></td>
                <td><?php echo $m['detail']; ?></td>
                <td><?php echo $m['client']; ?></td>
                <td><?php echo $m['executor']; ?></td>
                <td><?php echo $m['month'] ? Yii::t('app', $months[$m['month']]) : ''; ?></td>
                <td><?php echo $m['office']; ?></td>
                <td><?php echo $m['code']; ?></td>
				<!--
                <td class="warning"><?php //echo number_format($m['av_plus'], 2, ',', ' '); ?></td>
                <td class="info"><?php //echo number_format($m['b_plus'], 2, ',', ' '); ?></td>
                -->
				<td class="success"><?php echo number_format($m['n_plus'], 2, ',', ' '); ?></td>
                <!--
				<td class="warning"><?php //echo number_format($m['av_minus'], 2, ',', ' '); ?></td>
                <td class="info"><?php //echo number_format($m['b_minus'], 2, ',', ' '); ?></td>
                -->
				<td class="success"><?php echo number_format($m['n_minus'], 2, ',', ' '); ?></td>
                <!--
				<td class="warning"><?php
                    //$av_sum = ($m['av_plus'] + $av_sum) - $m['av_minus'];
                    //echo number_format($av_sum, 2, ',', ' ');
                ?></td>
                <td class="info"><?php
                    //$b_sum = ($m['b_plus'] + $b_sum) - $m['b_minus'];
                    //echo number_format($b_sum, 2, ',', ' ');
                ?></td>
                -->
				<td class="success"><?php
                    $n_sum = $m['n_plus'] - $m['n_minus'];
					if($m['done']==1) {
						$n_plus += $m['n_plus'];
						$n_minus += $m['n_minus'];
						$total_sum += $n_sum;
					}
                    echo number_format($n_sum, 2, ',', ' ');
                ?></td>
				<td><?php
				    if(($m['done']!=1 && $m['reviewed']!=1 && \Yii::$app->session->get('user.ustatus')==4) || \Yii::$app->session->get('user.ustatus')==3) {
						echo Html::a('', ['update', 'id'=>$m['id']], ['class'=>'glyphicon glyphicon-pencil', 'title'=>\Yii::t('app','Edit')]);
					}				
				    if($m['done']!=1) {
						if($m['reviewed']!=1) {
							if(\Yii::$app->session->get('user.ustatus')==3) {
								echo ' ';
								echo Html::a('', ['check', 'id'=>$m['id']], ['class'=>'glyphicon glyphicon-ok-circle', 'title'=>\Yii::t('app','Check')]);
							}
						}
						if($m['reviewed']==1) {
							echo ' ';
							echo Html::a('', ['uncheck', 'id'=>$m['id']], ['class'=>'glyphicon glyphicon-remove-circle', 'title'=>\Yii::t('app','Uncheck')]);
							echo ' ';
							echo Html::a('', ['done', 'id'=>$m['id']], ['class'=>'glyphicon glyphicon-ok-sign', 'title'=>\Yii::t('app','Execute')]);
						}						
					}
					elseif($m['done']==1) {
						echo ' ';
						echo Html::a('', ['undone', 'id'=>$m['id']], ['class'=>'glyphicon glyphicon-remove-sign', 'title'=>\Yii::t('app','Unexecute')]);
					}
				    if($m['done']!=1 && $m['reviewed']!=1 && (\Yii::$app->session->get('user.ustatus')==4 || \Yii::$app->session->get('user.ustatus')==3)) {
							echo ' ';
							echo Html::a('', ['delete', 'id'=>$m['id']], ['class'=>'glyphicon glyphicon-trash', 'title'=>\Yii::t('app','Delete')]);
					}
				?></td>
            </tr>
            <?php
            $i++;
            } ?>
        </tbody>
    </table>
	<p class="text-right"><strong>Приход: <?php echo number_format($n_plus, 2, ',', ' '); ?></strong></p>
	<p class="text-right"><strong>Расход: -<?php echo number_format($n_minus, 2, ',', ' '); ?></strong></p>
	<p class="text-right"><strong>Итого: <?php echo number_format($total_sum, 2, ',', ' '); ?></strong></p>
</div>