<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $tchrs controllers\ReportController.php */
/* @var $groups controllers\ReportController.php */
/* @var $lessons controllers\ReportController.php */
/* @var $pages controllers\ReportController.php */

if(Yii::$app->request->get('RTID')){
    $tid = Yii::$app->request->get('RTID');
} else {
    $tid = 'all';
}

// первый элемент страницы 
$start = 1;
// последний элемент страницы
$end = 10;
// следующая страница
$nextpage = 2;
// предыдущая страница
$prevpage = 0;
// проверяем не задан ли номер страницы
if(Yii::$app->request->get('page')){
		if(Yii::$app->request->get('page')>1){
		// считаем номер первой строки с учетом страницы
			$start = (10 * (Yii::$app->request->get('page') - 1) + 1);
		// считаем номер последней строки с учетом страницы
			$end = $start + 9;
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

if($tchrs) {

echo "<p class='text-right'>Показано ".$start." - ";
if($end>=$pages->totalCount) {
		echo $pages->totalCount;
}
else {
	echo $end;
}
echo " из ".$pages->totalCount."</p>";

// выводим кнопки паджинации
echo "<nav>";
echo "<ul class='pager'>";
echo "<li class='previous'>".(($prevpage>0) ? Html::a('Предыдущий',['report/index', 'type'=>8, 'page'=>$prevpage, 'RTID'=>$tid]) : '')."</li>";
echo "<li class='next'>".(($end<$pages->totalCount) ? Html::a('Следующий',['report/index', 'type'=>8, 'page'=>$nextpage, 'RTID'=>$tid]) : '')."</li>";
echo "</ul>";
echo "</nav>";

foreach($tchrs as $key => $value) {
    echo "<div class='row bg-info' style='padding: 10px'>";
	echo "<div class='text-left col-sm-8'><strong>".$value."</strong></div>";
	echo "<div class='text-right col-sm-4'><span class='label label-info' title='Количество занятий на проверке'>".$lcount[$key]['totalCount']."</span></div>";
	echo "</div>";
    foreach($groups as $g ) {
        if($g['tid']==$key) {
            echo "<div style='padding: 10px'>".Html::a("#".$g['gid']." ".$g['service'].", ур: ".$g['ename']." (усл.#".$g['sid'].")",['groupteacher/view','id'=>$g['gid']])."</div>";
			//echo "<div class='text-right col-sm-4'>".Html::a("<span class='label label-default'>".$lcount[$key][$g['gid']]['totalCount']."</span>", ['groupteacher/view','id'=>$g['gid']])."</div>";
			//echo "</div>";
            //echo "<div class='collapse' id='collapse-group-".$key."-".$g['gid']."'>";
			if($lcount[$key][$g['gid']]['totalCount'] > 0) {
				echo "<table class='table table-bordered table-stripped table-hover table-condensed' style='margin-bottom:10px'>";
				echo "<tbody>";
				foreach($lessons as $l) {
					if($l['gid']==$g['gid']&&$key==$l['tid']){
						echo "<tr".($l['visible']==0 ? " class='danger'" : "").">";
						echo "<td width='5%'>#".$l['lid']."</td>";
						//echo "<td width='2%'>".($l['done']==1 ? "<span class='glyphicon glyphicon-ok' aria-hidden='true'></span>" : "")."</td>";
						echo "<td width='15%'>".Html::a($l['date'].' →',['groupteacher/view','id'=>$l['gid'],'#'=>'lesson_'.$l['lid']])."</td>";
						echo "<td>".$l['desc']."</td>";
						echo "<td width='5%'>".$g['hours']." ч.</td>";
						echo "</tr>";
					}
				}
				echo "</tbody>";
				echo "</table>";
			}
            //echo "</div>";
         }
    }
}

// выводим кнопки паджинации
echo "<nav>";
echo "<ul class='pager'>";
echo "<li class='previous'>".(($prevpage>0) ? Html::a('Предыдущий',['report/index', 'type'=>8, 'page'=>$prevpage, 'RTID'=>$tid]) : '')."</li>";
echo "<li class='next'>".(($end<$pages->totalCount) ? Html::a('Следующий',['report/index', 'type'=>8, 'page'=>$nextpage, 'RTID'=>$tid]) : '')."</li>";
echo "</ul>";
echo "</nav>";

}

/*
$totalsum = 0;
foreach($dates as $key => $value){
    echo "<a href='#collapse-invoice-".$key."' role='button' data-toggle='collapse' aria-expanded='false' aria-controls='collapse-invoice-".$key."' class='text-warning'>".date('d.m.y', strtotime($value))." (".Yii::t('app', date('l', strtotime($value))).")</a><br />";
    echo "<div class='collapse' id='collapse-invoice-".$key."'>";
    $totaldaysum = 0;
    echo "<table class='table table-bordered table-stripped table-hover table-condensed'>";
    echo "<tbody>";
    foreach($invoices as $inv) {
        if($inv['date']==$value) {
            if($inv['visible']==0) {
                    echo "<tr class='danger'>";
            } else {
                if($inv['done']==1) {
                    echo "<tr class='success'>";
                } else {
                    echo "<tr class='warning'>";
                }
            }
            echo "<td>#".$inv['iid'].($inv['remain']==1 ? ' (ост.)' : '')."</td>";
            echo "<td>".$inv['uname']."</td>";
            echo "<td>".Html::a($inv['sname']." → ", ['studname/view', 'id'=>$inv['sid']])." (усл. #".$inv['id'].", ".$inv['num']." зан.)</td>";
            echo "<td>".$inv['money']."</td>";
            echo "</tr>";
            if($inv['visible']==1&&$inv['remain']==0) {
                $totaldaysum = $totaldaysum + $inv['money'];
            }
            }
        }
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
    echo "<p class='text-right'>всего за день: ".$totaldaysum."</p>";
    $totalsum = $totalsum + $totaldaysum;
}
echo "<hr />";
echo "<p class='text-right'>всего по офису: ".$totalsum."</p>";
*/
?>

