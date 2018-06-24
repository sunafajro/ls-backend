<?php
use yii\helpers\Html;

// если в запросе GET передан параметр TSS
if(Yii::$app->request->get('TSS')){
	// задаем переменную
    $tss = Yii::$app->request->get('TSS');
} else {
	// по умолчанию пустая строка
    $tss = '';
}
// если в запросе GET передан параметр OID
if(Yii::$app->request->get('OID')){
    // задаем переменную
    $oid = Yii::$app->request->get('OID');
} else {
    // по умолчанию пустая строка
    $oid = '';
}

// если в запросе GET передан параметр type (тип отчета)
if(Yii::$app->request->get('type')){
    // задаем переменную 
    $type = Yii::$app->request->get('type');
} else {
    $type = 6;
}

// проверяем не задан ли тип долга в GET
if(Yii::$app->request->get('SIGN')){
	if(Yii::$app->request->get('SIGN')==1) {
        $sign = 1;
	} elseif(Yii::$app->request->get('SIGN')==2) {
		$sign = 2;
	} else {
		$sign = 'all';
	}
} else {
    $sign = 1;
}

// проверяем не задан ли тип долга в GET
if(Yii::$app->request->get('STATE')){
	if(Yii::$app->request->get('STATE')==1) {
        $state = 1;
	} elseif(Yii::$app->request->get('STATE')==2) {
		$state = 2;
	} else {
		$state = 'all';
	}
} else {
    $state = 1;
}

// первый элемент страницы 
$start = 1;
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
echo "<li class='previous'>".(($prevpage>0) ? Html::a('Предыдущий',['report/index','page'=>$prevpage,'TSS'=>$tss,'type'=>$type,'OID'=>$oid,'SIGN'=>$sign,'STATE'=>$state]) : '')."</li>";
echo "<li class='next'>".(($end<$pages->totalCount) ? Html::a('Следующий',['report/index','page'=>$nextpage,'TSS'=>$tss,'type'=>$type,'OID'=>$oid,'SIGN'=>$sign,'STATE'=>$state]) : '')."</li>";
echo "</ul>";
echo "</nav>";

foreach($stds as $st){
	echo "<div class='".($st['debt']>=0 ? 'bg-success text-success' : 'bg-danger text-danger')."' style='padding: 15px'>";
    echo "<div style='float:left'><strong>".Html::a("#".$st['id']." ".$st['name']." →", ['studname/view', 'id'=>$st['id']])."</strong></div>";
    echo "<div class='text-right'><strong>(баланс: ".$st['debt']." р.)</strong></div>";
    echo "</div>";
    echo "<table class='table table-bordered table-stripped table-hover table-condensed'>";
    echo "<tbody>";
    foreach($students as $s){
    	if($s['stid']==$st['id']){
    		echo "<tr".($s['num']>=0 ? "" : " class='danger'").">";
    		echo "<td>услуга #".$s['sid']." ".$s['sname'],"</td><td class='text-right' width='10%'>".$s['num']." зан.</td>";
    		echo "</tr>";
    	}
    }
	unset($s);
    echo "</tbody>";
    echo "</table>";

}
unset($st);
unset($students);
unset($stdnts);

// выводим кнопки паджинации
echo "<nav>";
echo "<ul class='pager'>";
echo "<li class='previous'>".(($prevpage>0) ? Html::a('Предыдущий',['report/index','page'=>$prevpage,'TSS'=>$tss,'type'=>$type,'OID'=>$oid,'SIGN'=>$sign,'STATE'=>$state]) : '')."</li>";
echo "<li class='next'>".(($end<$pages->totalCount) ? Html::a('Следующий',['report/index','page'=>$nextpage,'TSS'=>$tss,'type'=>$type,'OID'=>$oid,'SIGN'=>$sign,'STATE'=>$state]) : '')."</li>";
echo "</ul>";
echo "</nav>";
?>
