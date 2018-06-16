<?php
use yii\helpers\Html;

	// сообщение об ошибке при начислении
    if(Yii::$app->session->hasFlash('error')) {
        echo "<div class='alert alert-danger' role='alert'>";
        echo Yii::$app->session->getFlash('error');
        echo "</div>";
    }
    // сообщение об успешном начислении   
    if(Yii::$app->session->hasFlash('success')) {
        echo "<div class='alert alert-success' role='alert'>";
        echo Yii::$app->session->getFlash('success');
        echo "</div>";
    }

    if(!$tid || $tid=='all'){
        $current = 1;
        $start = 1;
        $end = 10;
        $prevpage = 0;
        $nextpage = 2;
        if(Yii::$app->request->get('page')){
            $current = (int)Yii::$app->request->get('page');
            $start = 10 * (int)Yii::$app->request->get('page') - 9;
            $end = 10 * (int)Yii::$app->request->get('page');
            if($end>$pages){
                $end = $pages;
            }
                $prevpage = (int)Yii::$app->request->get('page') - 1;
                $nextpage = (int)Yii::$app->request->get('page') + 1;
        }

        echo "<nav>";
        echo "<ul class='pager'>";
        echo "<li class='previous'>".(($start>1) ? Html::a('Предыдущий',['report/index','type'=>9,'page'=>$prevpage]) : '')."</li>";
        echo "<li class='next'>".(($end<$pages) ? Html::a('Следующий',['report/index','type'=>9, 'page'=>$nextpage]) : '')."</li>";
        echo "</ul>";
        echo "</nav>";
    }


// задаем общую сумму по начислениям
$totalAccural = 0;
// начинаем с распечатывания преподавателей
foreach($teachers as $teacher)
         {
            echo "<div class='panel panel-default'>";
	    // распечатываем преподавателя и ставку
            echo "<div class='panel-heading'>".Html::a($teacher['name'],['teacher/view', 'id'=>$teacher['id']])."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ставка: ".$teacher['norm']." р.</div>";
            echo "<div class='panel-body'>";
			// задаем переменные часов и сумм начислений
			$time = 0;
			$money = 0;
			foreach($groups as $group){
				if($teacher['id']==$group['tid']){
				echo "<table class='table table-condensed'>";
				echo "<thead>";
				// выводим название и параметры группы
				echo "<th colspan='7'>#".$group['gid']." ".$group['course'].", ур. ".$group['level']." (усл.#".$group['service']."), ".$group['office']."</th>";
				// выводим кнопку для начисления вознаграждения за занятие
				echo "<th class='text-right'>".Html::a("Начислить ".$group['time']." ч.",['accrual/addaccrual','gid'=>$group['gid'],'tid'=>$teacher['id']], ['class'=>'btn btn-xs btn-success'])."</th>";
				echo "<tbody>";
				foreach($lessons as $lesson){
					if($lesson['tid']==$group['tid'] && $lesson['gid']==$group['gid']){
						// задаем первоначальный коэффициент 1 и количество учеников 0
						//$koef = 1;
						//$pupil = 0;
						// распечатываем массив с количеством студентов присутствовавших на занятии
						/*foreach($pupilscount as $pupilcount){
							// сверяем группу и преподавателя
							if($pupilcount['gid']==$lesson['gid'] && $pupilcount['tid']==$lesson['tid'] && $pupilcount['jid']==$lesson['jid']){
							//выбираем коэффициент в зависимости от количества учеников
							switch($pupilcount['num']){
								case 1: $koef = 1; break;
								case 2: $koef = 1; break;
								case 3: $koef = 1; break;
								case 4: $koef = 1.1; break;
								case 5: $koef = 1.2; break;
								case 6: $koef = 1.3; break;
								case 7: $koef = 1.4; break;
								case 8: $koef = 1.5; break;
								case 9: $koef = 1.6; break;
								case 10: $koef = 1.7; break;
							}
							// сохраняем колич учеников для послед вывода в табличку
							$pupil = $pupilcount['num'];
							}
						}*/
						echo "<tr>";
						echo "<td width='5%'>";
						switch($lesson['edutime']){
							case 1: echo Html::img('images/day.png');break;
							case 2: echo Html::img('images/night.png');break;
							case 3: echo Html::img('images/halfday.png');break;
						}
						echo "</td>";
						// выводим id занятия
						echo "<td width='10%'>#".$lesson['jid']."</td>";
						// если занятие проверено выводим галку
						echo "<td width='5%'>".($lesson['view'] ? '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>' : '')."</td>";
						// выводим дату занятия в виде ссылки на группу
						echo "<td width='10%'>".Html::a($lesson['jdate'],['groupteacher/view','id'=>$group['gid']])."</td>";
						// выводим колич учеников
						echo "<td width='5%'>".$lesson['pcount']." чел.</td>";
						// выводим описание занятия
						echo "<td>".$lesson['desc']."</td>";
						// выводим продолжительность занятия
						echo "<td class='text-right' width='5%'>".$lesson['time']." ч.</td>";
						// выводим оплату за занятие
						echo "<td class='text-right' width='5%'>".$lesson['money']." р.</td>";
						echo "</tr>";
						// суммируем продолжительность с общим колич часов занятий по данной группе
						$time += $lesson['time'];
						$money += $lesson['money'];
						// считаем сумму начисления
						/*switch($lesson['edutime']){
							// дневное время
							case 1: 
								// для руководителей ставка 0
								if(in_array($teacher['id'],$admins)){
									$money += (($teacher['norm'] - $teacher['norm']) * $lesson['time'])*$koef;
								}// для остальных -50р
								 else {
									$money += (($teacher['norm'] - 50) * $lesson['time'])*$koef;
								}
								break;
							// вечернее время
							case 2: $money += ($teacher['norm']*$lesson['time'])*$koef;break;
							// полурабочее время
							case 3: $money += (($teacher['norm']*$lesson['time']*2)/3)*$koef; break; 
							}
							*/
					}
				}
			echo "</tbody>";
			echo "</table>";
		    }
		}
		echo "<p class='text-right'>всего к начислению за ".$time." ч. : ".$money." р.</p>";
	    echo "</div><!-- panel-body-->";
	    echo "</div><!-- panel -->";
	    $totalAccural += $money;
        }
	echo "<p class='text-right'>всего к начислению (без надбавок): <strong>".$totalAccural." р.</strong></p>";
    if(!$tid || $tid=='all') {
	echo "<nav>";
	echo "<ul class='pager'>";
	echo "<li class='previous'>".(($start>1) ? Html::a('Предыдущий',['report/index','type'=>9,'page'=>$prevpage]) : '')."</li>";
	echo "<li class='next'>".(($end<$pages) ? Html::a('Следующий',['report/index','type'=>9, 'page'=>$nextpage]) : '')."</li>";
	echo "</ul>";
	echo "</nav>";
    }
?>
