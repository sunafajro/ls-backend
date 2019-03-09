<?php
use yii\helpers\Html;
use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;
use yii\widgets\Menu;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
$this->title = 'Система учета :: ' . \Yii::t('app','Reports');
$this->params['breadcrumbs'][] = \Yii::t('app','Reports');

switch(Yii::$app->request->get('type')){
    case 1: $viewsfile = 'common'; $type = 1; break;
	case 2: $viewsfile = 'marzha1'; $type = 2; break;
    case 4: $viewsfile = 'studpay'; $type = 4; break;
    case 5: $viewsfile = 'studinv'; $type = 5; break;
    case 6: $viewsfile = 'studdebt'; $type = 6; break;
    case 8: $viewsfile = 'studjour'; $type = 8; break;
    case 9: $viewsfile = 'accruals'; $type = 9; break;
	case 10: $viewsfile = 'studacdebt'; $type = 10; break;
    default: 
        if(\Yii::$app->session->get('user.ustatus')==3 || \Yii::$app->session->get('user.ustatus')==8) {
            $viewsfile = 'common'; $type = 1;
        }
        elseif(\Yii::$app->session->get('user.ustatus')==4) {
            $viewsfile = 'studjour'; $type = 8;
        }
}

if(\Yii::$app->request->get('year')){
    $year = \Yii::$app->request->get('year');
} else {
    $year = date('Y');
}

if(\Yii::$app->request->get('MON')) {
    if(\Yii::$app->request->get('MON')>=1 && \Yii::$app->request->get('MON')<=12) {
        $mon = \Yii::$app->request->get('MON');
    } else {
        $mon = NULL;
    }
} else {
    $mon = NULL;
}

if(\Yii::$app->request->get('RWID')){
    $week = \Yii::$app->request->get('RWID');
} else {
// надо разобраться с этой фигней про плавающие недели!!!
//    if(date('W', strtotime($year.'-01-01'))!= '01'){
//       $week = date('W') + 1;
//    } else {
        $week = date('W');
//    }  
}
// проверяем не задал ли преподаватель
if(\Yii::$app->request->get('RTID') && \Yii::$app->request->get('RTID')!='all'){
    $tid = \Yii::$app->request->get('RTID');
} else {
    $tid = 'all';
}

// проверяем не задан ли офис в GET
if(\Yii::$app->request->get('OID')){
    $oid = \Yii::$app->request->get('OID');
} else {
    $oid = 'all';
}

// проверяем не задан ли тип долга в GET
if(\Yii::$app->request->get('SIGN')){
	if(\Yii::$app->request->get('SIGN')==1) {
        $sign = 1;
	} elseif(\Yii::$app->request->get('SIGN')==2) {
		$sign = 2;
	} else {
		$sign = 'all';
	}
} else {
    $sign = 1;
}

// проверяем не задан ли тип долга в GET
if(\Yii::$app->request->get('STATE')){
	if(Yii::$app->request->get('STATE')==1) {
        $state = 1;
	} elseif(\Yii::$app->request->get('STATE')==2) {
		$state = 2;
	} else {
		$state = 'all';
	}
} else {
    $state = 1;
}

//if(date('W', strtotime($year) != '01'){
//    $startweek = 0;
//    $endweek = 52;
//}

// если в запросе GET передан параметр TSS
if(\Yii::$app->request->get('TSS')){
        // задаем переменную
    $tss = \Yii::$app->request->get('TSS');
} else {
        // по умолчанию пустая строка
    $tss = '';
}

?>
<div class="report-index">
<?php
    // формируем выпадающий список с типами отчетов для руководителей
    if(\Yii::$app->session->get('user.ustatus')==3) {

        $items[] = ['label' => Yii::t('app', 'Reports'), 'url' => '#', 'items'=> [
                ['label' => Yii::t('app','Common'), 'url' => ['report/common']],
				['label' => Yii::t('app','Margin'), 'url' => ['report/margin']],
                ['label' => Yii::t('app','Payments'), 'url' => ['report/payments']],
                ['label' => Yii::t('app','Invoices'), 'url' => ['report/index','type'=>5]],
                ['label' => Yii::t('app','Debts'), 'url' => ['report/debt']],
                ['label' => Yii::t('app','Journals'), 'url' => ['report/journals']],
                ['label' => Yii::t('app', 'Accruals'), 'url' => ['report/accrual']],
                ['label' => \Yii::t('app', 'Salaries'), 'url' => ['report/salaries']],
                ['label' => Yii::t('app', 'Office plan'), 'url' => ['report/plan']]
        ]];
    }
    // формируем выпадающий список с типами отчетов для менеджеров
    elseif(Yii::$app->session->get('user.ustatus')==4) {
        $items[] = ['label' => Yii::t('app', 'Reports'), 'url' => '#', 'items'=> [
            ['label' => Yii::t('app','Payments'), 'url' => ['report/payments']],
            ['label' => Yii::t('app','Invoices'), 'url' => ['report/index','type'=>5]],
            ['label' => Yii::t('app','Debts'), 'url' => ['report/debt']],
            ['label' => Yii::t('app','Journals'), 'url' => ['report/journals']]
        ]];
    }
    // формируем выпадающий список с типами отчетов для бухгатера
    elseif(Yii::$app->session->get('user.ustatus')==8) {
        $items[] = ['label' => Yii::t('app', 'Reports'), 'url' => '#', 'items'=> [
            ['label' => Yii::t('app','Common'), 'url' => ['report/common']],
            ['label' => Yii::t('app','Payments'), 'url' => ['report/payments']],
            ['label' => Yii::t('app', 'Accruals'), 'url' => ['report/accrual']],
            ['label' => Yii::t('app', 'Salaries'), 'url' => ['report/salaries']]
        ]];
    }

    // выводим меню
    NavBar::begin([
        'renderInnerContainer' => false,
    ]);
	echo Nav::widget([
		'options' => ['class' => 'navbar-nav navbar-left'],
		'items' => $items,
		]);
	$form = ActiveForm::begin([
        'method' => 'get',
        'options' => ['class' => 'navbar-form navbar-right'],
        'action' => ['report/index', 'type' => $type],
	]);
	if($type==1 || $type==2 || $type==4 || $type==5) {
		if($type != 2) {
    ?>
        <div class="form-group">
        <select name="RWID" class="form-control input-sm">
        <option value="all"><?php echo \Yii::t('app', '-all weeks-') ?></option>
    <?php
        for ($week_number=0; $week_number<53; $week_number++) {
            $first_day = date('d/m', $week_number * 7 * 86400 + strtotime('1/1/' . $year) - date('w', strtotime('1/1/' . $year)) * 86400 + 86400);
            $last_day = date('d/m', ($week_number + 1) * 7 * 86400 + strtotime('1/1/' . $year) - date('w', strtotime('1/1/' . $year)) * 86400);
            echo "<option value='".($week_number+1)."'".(($week_number+1)==$week ? ' selected' : '').">#".($week_number+1)." ".$first_day."-".$last_day."</option>";
        }
    ?>
        </select>
        </div>
		<?php } ?>
        <select class='form-control input-sm' name='MON'>";
        <option value='all'><?= Yii::t('app', '-all months-') ?></option>";
    <?php
        //генерим массив с названиями месяцев
        //for($i=1;$i<=12;$i++){
        //    $months[date('n',strtotime("$i month"))]=date('F',strtotime("$i month"));
        //}
        //ksort($months);
        // распечатываем список месяцев в селект
        foreach($months as $mkey => $mvalue){ ?>
            <option value="<?php echo $mkey; ?>" <?php echo ($mkey==$mon) ? ' selected' : ''; ?>><?php echo $mvalue; ?></option>
        <?php } ?>
        </select>
        <div class="form-group">
        <select name="year" class="form-control input-sm">
        <?php
		for ($y=2011; $y<=date('Y'); $y++) { ?>
            <option value="<?php echo $y; ?>"<?php echo ($year==$y) ? ' selected' : ''; ?>><?php echo $y; ?></option>
        <?php } ?>
        </select>
        </div>
<?php } 
	if($type==6) {
		// фильтр по имени студента
		echo "<div class='form-group'>";
		echo "<input type='text' class='form-control input-sm' placeholder='Найти...' name='TSS'";
		echo $tss!='' ? "value='".$tss."'>" : '>';
		echo "</div>";
		// селект с состоянием студента
		echo "&nbsp;";
		echo "<div class='form-group'>";
		echo "<select name='STATE' class='form-control input-sm'>";
		echo "<option value='all'>-все студенты-</option>";
        echo "<option value='1'".($state==1 ? ' selected' : '').">С нами</option>";
        echo "<option value='2'".($state==2 ? ' selected' : '').">Не с нами</option>";
		echo "</select>";
		echo "</div>";
		// фильтр с типом долга студента
		echo "&nbsp;";
		echo "<div class='form-group'>";
		echo "<select name='SIGN' class='form-control input-sm'>";
		echo "<option value='all'>-все долги-</option>";
        echo "<option value='1'".($sign==1 ? ' selected' : '').">Нам должны</option>";
        echo "<option value='2'".($sign==2 ? ' selected' : '').">Мы должны</option>";
		echo "</select>";
		echo "</div>";		
        if(Yii::$app->session->get('user.ustatus')==3) {
			echo "&nbsp;";
			echo "<div class='form-group'>";
			echo "<select name='OID' class='form-control input-sm'>";
			echo "<option value='all'>-все офисы-</option>";
			foreach($offices as $o) {
				echo "<option value='".$o['oid']."'".($oid==$o['oid'] ? ' selected' : '').">".mb_substr($o['oname'], 0, 16)."</option>";
			}
			unset($o);
			echo "</select>";
			echo "</div>";
        }
    }
    if($type==8) {
            echo "<div class='form-group'>";
            echo "<select name='RTID' class='form-control input-sm'>";
            echo "<option value='all'>-все преподаватели-</option>";
            foreach($teachers as $t) {
                echo "<option value='".$t['tid']."'".($tid==$t['tid'] ? ' selected' : '').">".$t['tname']."</option>";
            }
            unset($t);
            echo "</select>";
            echo "</div>";
    }
    if($type==9) {
        echo "<div class='form-group'>";
        echo "<select class='form-control input-sm' name='RTID'>";
        echo "<option value='all'>-все преподаватели-</option>";
		// распечатываем преподавателей в селект
		foreach($tchrs as $key => $name){
			echo "<option";
			echo ($key==$tid) ? ' selected ' : ' ';
			echo "value='".$key."'>".$name;
			echo "</option>";
		}
        unset($key);
		unset($name);
        echo "</select>";
        echo "</div>";	
	}
    echo " ";
    echo "<button type='submit' class='btn btn-default btn-sm'>GO</button>";

        ActiveForm::end();
        NavBar::end();
	?>

<?=
    $this->render($viewsfile, [
        'payments' => $payments,
        'invoices'=>$invoices,
        'students'=>$students,
        'stds' => $stds,
        'debt' => $debt,
        'lessons'=>$lessons,
        'teachers'=>$teachers, 
        'tchrs' => $tchrs,
        'groups'=>$groups,
        'dates' => $dates,
        'common_report' => $common_report,
        'pages'=>$pages,
        'lcount'=>$lcount,
        'tid'=>$tid,
        'offices'=>$offices,
    ]) ?>

</div>

