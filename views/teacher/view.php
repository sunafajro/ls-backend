<?php

use yii\helpers\Html;
use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;
use yii\widgets\Menu;

/* @var $this yii\web\View */
/* @var $model app\models\CalcTeacher */
/* @var $teachertax */
/* @var $teacherschedule */
/* @var $teacherdata */

$this->title = $model->name;
if ((int)Yii::$app->session->get('user.ustatus') !== 5) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app','Teachers'), 'url' => ['index']];
} else {
$this->params['breadcrumbs'][] = Yii::t('app','Teachers');
}
$this->params['breadcrumbs'][] = $this->title;

//формируем массив со списком названий дней недели
for($i=0; $i<7; $i++){
    $days[date('N', strtotime('+'.$i.' day'))]=date('D', strtotime('+'.$i.' day'));
}
ksort($days);
// формируем список дней в которые есть занятия по расписанию
$i = 0;
foreach($teacherschedule as $sched){
    $sscheddays[$i]=$sched['day'];
    $i++;
}
//если нет занятий в расписании, создаем пустой массив
if(empty($sscheddays)){
    $sscheddays[0] = 0;
}
// оставляем только уникальные значения
$scheddays = array_unique($sscheddays);
// сортируем по порядку
ksort($scheddays);
// проверяем какие данные выводить в карочку преподавателя: 1/2 - группы, 3 - начисления; 4 - выплаты фонда
if(Yii::$app->request->get('tab')){
	$tab = Yii::$app->request->get('tab');
} else {
    if(Yii::$app->session->get('user.ustatus')==8) {
        $tab = 3;
    } else {
        $tab = 1;
    }	
}
// выбираем даты начислений
if($tab == 3){
    if(!empty($teacherdata)){
	$i = 0;
	foreach($teacherdata as $accrual){
			$saccrualdates[$i]=$accrual['date'];
			$i++;
	}
	// оставляем только уникальные значения
	$accrualdates = array_unique($saccrualdates);
	// сортируем в обратном порядке
	rsort($accrualdates);
	}
}
?>
<!-- начало контент области -->
<div class="row row-offcanvas row-offcanvas-left teacher-view">
    <!-- левая боковая панель -->
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= $userInfoBlock ?>
        <?php if ((int)Yii::$app->session->get('user.ustatus') === 3 || (int)Yii::$app->session->get('user.ustatus') === 4 || (int)Yii::$app->session->get('user.uid') === 296) : ?>
            <h4><?= Yii::t('app', 'Actions') ?>:</h4>
            <?php if ((int)Yii::$app->session->get('user.uid') !== 296) : ?>
              <?= Html::a('<span class="fa fa-users" aria-hidden="true"></span> ' . Yii::t('app', 'Add group'), ['groupteacher/create','tid'=>$model->id], ['class' => 'btn btn-default btn-sm btn-block', 'title' => Yii::t('app','Add group')]) ?>
              <?= Html::a('<span class="fa fa-language" aria-hidden="true"></span> ' . Yii::t('app', 'Add language'), ['langteacher/create','tid'=>$model->id], ['class' => 'btn btn-default btn-sm btn-block', 'title' => Yii::t('app','Add language')]) ?>
            <?php endif; ?>
            <?php if ((int)Yii::$app->session->get('user.uid') !== 296 && (int)Yii::$app->session->get('user.ustatus') !== 4) : ?>
                <?= Html::a('<span class="fa fa-money" aria-hidden="true"></span> ' . Yii::t('app', 'Add rate'), ['edunormteacher/create','tid'=>$model->id], ['class' => 'btn btn-default btn-sm btn-block', 'title' => Yii::t('app','Add rate')]) ?>
                <?= Html::a('<span class="fa fa-gift" aria-hidden="true"></span> ' . Yii::t('app', 'Add premium'), ['teacherlangpremium/create','tid'=>$model->id], ['class' => 'btn btn-default btn-sm btn-block', 'title' => Yii::t('app','Add premium')]) ?>
            <?php endif; ?>
            <?= Html::a('<span class="fa fa-pencil" aria-hidden="true"></span> ' . Yii::t('app', 'Edit'), ['teacher/update','id'=>$model->id], ['class' => 'btn btn-warning btn-sm btn-block', 'title' => Yii::t('app','Edit teacher')]) ?>
            <?php if ((int)Yii::$app->session->get('user.uid') !== 296 && (int)Yii::$app->session->get('user.ustatus') !== 4) : ?>
                <?= Html::a('<span class="fa fa-trash" aria-hidden="true"></span> ' . Yii::t('app', 'Delete'), ['teacher/delete','id'=>$model->id], ['class' => 'btn btn-danger btn-sm btn-block', 'title' => Yii::t('app','Delete teacher')]) ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <!-- левая боковая панель -->
    <!-- центральная область -->
    <div id="content" class="col-sm-8">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
        <?php if(Yii::$app->session->hasFlash('error')): ?>
		    <div class="alert alert-danger" role="alert"><?= Yii::$app->session->getFlash('error') ?></div>
        <?php endif; ?>    
        <?php if(Yii::$app->session->hasFlash('success')): ?>
		    <div class='alert alert-success' role='alert'><?= Yii::$app->session->getFlash('success'); ?></div>
        <?php endif; ?> 
        <h4>
            <?= isset($model->name) && $model->name != '' ? $model->name : '' ?>
            <?= isset($model->birthdate) && $model->birthdate != '' ? ' :: ' . date('d.m.y', strtotime($model->birthdate)) : '' ?>
            <?= isset($model->phone) && $model->phone != '' ? ' :: ' . $model->phone : '' ?>
            <?= isset($model->email) && $model->email != '' ? ' :: ' . $model->email : '' ?>
            <?= isset($model->social_link) && $model->social_link != '' ? ' :: ' . Html::a('', 'http://'.$model->social_link, ['class'=>'glyphicon glyphicon-new-window', 'target'=>'_blank', 'title'=>Yii::t('app', 'Link to social profile')]) : '' ?>
            <?php
                if (!empty($teachertax)) {
                  $places = []; 
                  foreach($teachertax as $tax) {
                    $str  = '<span class="label ' . ((int)$tax['tjplace'] === 1 ? 'label-success' : 'label-info') . '">';
                    $str .= $jobPlace[$tax['tjplace']] . '</span>';
                    $places[$tax['tjplace']] = $str;
                  }
                  ksort($places);
                  echo ' :: ' . implode($places, ' / ');
                }
            ?>
        </h4>
        <?= $model->address ? '<p><b>' . Yii::t('app', 'Address') . ':</b> <i>' . $model->address . '</i></p>' : '' ?>
        <?php
        if((int)Yii::$app->session->get('user.ustatus') === 3 || (int)Yii::$app->session->get('user.uteacher') === (int)$model->id || (int)Yii::$app->session->get('user.uid') === 296) : ?>
		  <?php $ttax = []; ?>
          <?php
          if (!empty($teachertax)) : ?>
            <?php
            foreach($teachertax as $tax) : ?>
                <strong>Ставка преподавателя:</strong> <?= $tax['taxname'] ?> <small><em><span class='inblocktext'>(назначена  <?= date('d.m.y', strtotime($tax['taxdate'])) ?></span></em>
                <span class="label <?= ((int)$tax['tjplace'] === 1 ? 'label-success' : 'label-info') ?>"><?= $jobPlace[$tax['tjplace']] ?></span>
		        <?php $ttax[(int)$tax['tjplace']] = $tax['taxvalue']; ?>
                )</small><br />
            <?php endforeach; ?>
          <?php endif; ?>
        <?php endif; ?>
        <p></p>
        <a href='#collapse-schedule' role='button' data-toggle='collapse' aria-expanded='false' aria-controls='collapse-schedule' class='text-warning'>показать/скрыть расписание занятий</a>
        <div class="collapse" id="collapse-schedule">
          <?php
              foreach($days as $key=>$value){
                if(in_array($key, $scheddays)){
                  echo "<p><strong>" . Yii::t('app', $value) . "</strong></br>";
                  foreach($teacherschedule as $schedule){
                    if($schedule['day']==$key){
                      echo "<span class='inblocktext'><small>".date('H:i', strtotime($schedule['time_begin']))." - ".date('H:i', strtotime($schedule['time_end']))."</span>&nbsp;";
                      $link = "#".$schedule['gid']." ".$schedule['service'].", ".$schedule['level'];
                      echo Html::a($link, ['groupteacher/view','id'=>$schedule['gid']]);
                      echo "&nbsp;<span class='inblocktext'>(".$schedule['cnt'].")&nbsp;".$schedule['office']."&nbsp;".$schedule['room']."</small><br />";
                    }
                  } 
                  echo "</p>";
                }
              }
           ?>
        </div>
        <br />
        <?php if ((int)Yii::$app->session->get('user.ustatus') === 3 || (int)Yii::$app->session->get('user.uid') === 296) : ?>
        <!-- блок со списком проверенны занятий -->
        <a href='#collapse-accruals' role='button' data-toggle='collapse' aria-expanded='false' aria-controls='collapse-accruals' class='text-warning'>показать/скрыть занятия для начисления</a>
        <div class="collapse" id="collapse-accruals">
        <?php
        if (!empty($viewedlessons)) {
	    foreach($viewedlessons as $viewed){
            echo "<div class='panel panel-default'>";
            echo "<div class='panel-body'>";
            switch($viewed['edutime']){
                case 1: echo Html::img('@web/images/day.png', ['alt'=>'Дневное время', 'title'=>'Дневное время']); break;
                case 2: echo Html::img('@web/images/night.png', ['alt'=>'Вечернее время', 'title'=>'Вечернее время']); break; 
                case 3: echo Html::img('@web/images/halfday.png', ['alt'=>'Полурабочее время', 'title'=>'Полурабочее время']); break;
                default: echo '';
            }            
	        echo " <small>";
            $link = "Занятие #".$viewed['jid']." в группе #".$viewed['gid'];
            echo "<span class='inblocktext'>";
            echo Html::a($link,['groupteacher/view','id'=>$viewed['gid'], '#'=>'lesson_'.$viewed['jid']]);
            unset($link);
            echo "<br/ >";
            echo $viewed['service'].", ур: ".$viewed['level'].", ".$viewed['time']." ч., кол.ч. " . $viewed['pcount'];
            echo "<br />";
            echo date('d.m.y', strtotime($viewed['jdate']))." (".Yii::t('app', date('l', strtotime($viewed['jdate'])))."), ".$viewed['office'].", ";
            echo "коэф.: " . $viewed['koef'] . ", к начислению: " . $viewed['accrual'];
            echo ' р. <i>(ставка ' . $viewed['tax'] . 'р.)</i></span></small>';
            echo "</div>";
            echo "</div>";
	    }}
        ?>
        </div>
        <!-- блок со списком проверенны занятий -->
        <?php endif; ?>
        <p></p>
        <!-- блок с табами -->
        <ul class="nav nav-tabs">
        <?php if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.uteacher')==$model->id||Yii::$app->session->get('user.ustatus')==4||Yii::$app->session->get('user.ustatus')==6){
            echo "<li role='presentation'".(($tab == 1) ? " class='active'" : "").">".Html::a(Yii::t('app','Active groups'),['teacher/view','id'=>$model->id,'tab'=>1])."</li>";
            echo "<li role='presentation'".(($tab == 2) ? " class='active'" : "").">".Html::a(Yii::t('app','Finished groups'),['teacher/view','id'=>$model->id,'tab'=>2])."</li>";
        }
        if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.uteacher')==$model->id||Yii::$app->session->get('user.ustatus')==8){
            echo "<li role='presentation'".(($tab == 3) ? " class='active'" : "").">".Html::a(Yii::t('app','Accruals'),['teacher/view','id'=>$model->id,'tab'=>3])."</li>";
        }
        ?>
	    </ul>
        <!-- блок с табами -->
       <?php
        // выводим информаию о группах
        if($tab == 1 || $tab == 2) {
        echo "<p></p>";
        // задаем форму обучения. 1 - индивидуальные, 2 - группа, 3 - минигруппа, 4 - без привязки
        $eduform = [0, 1, 3, 2, 4];
        // делаем цикл в три шага для вывода групп по формам обучения
        for($a=1;$a<=4;$a++) {
            // Делаем разворачивающийся блок
            echo "<a href='#collapse-groupact-".$a."' role='button' data-toggle='collapse' aria-expanded='false' aria-controls='collapse-groupact-".$a."' class='text-warning'>";
            switch($eduform[$a]) {
                case 1: echo "<p>Индивидуально (".$efm['individual'].")</p>"; break;
                case 2: echo "<p>Группа (".$efm['group'].")</p>"; break;
                case 3: echo "<p>Минигруппа (".$efm['minigroup'].")</p>"; break;
                case 4: echo "<p>Без привязки к форме (".$efm['other'].")</p>"; break;
            }
            echo "</a>";
            echo "<div class='collapse' id='collapse-groupact-".$a."'>";
            // распечатываем массив
            foreach($teacherdata as $groupact) {
                // если форма обучения совпадает - выводим
                if($eduform[$a]==$groupact['eduform']) {
                    echo "<div class='panel panel-default'><div class='panel-body'>";
                    // первая строка
                    echo '<p>';
                    echo '<strong>Группа #' . $groupact['gid']. ' ' . $groupact['service'] . ' (Услуга: #' . $groupact['sid'] . '), Ур: ' . $groupact['level'] . '</strong> ';
                    echo '<span class="label ' . ((int)$groupact['direction'] === 1 ? 'label-success' : 'label-info') . '">';
                    echo $jobPlace[(int)$groupact['direction']] . '</span>';
                    echo '</p>';
                    // вторая строка
                    echo "<p>".Html::a("Журнал",['groupteacher/view', 'id'=>$groupact['gid']]);
                    echo "&nbsp;&nbsp;&nbsp;";
                    echo Html::a("Состав группы",['groupteacher/addstudent', 'gid'=>$groupact['gid']]);
                    // прячем кнопки от всех кроме руководителей
                    if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4){
                        echo "&nbsp;&nbsp;&nbsp;";
                        // для активной группы выводим ссылку на завершение
                        if($groupact['visible']==1){
                            echo Html::a(Yii::t('app','Finish'),['groupteacher/disable', 'id'=>$groupact['gid'], 'lid'=>$model->id]);
                        }
                        // для завершенной группы выводим ссылку на возобновление 
                        else {
                            echo Html::a(Yii::t('app','To active'),['groupteacher/enable', 'id'=>$groupact['gid'], 'lid'=>$model->id]);
                        }
                        echo "&nbsp;&nbsp;&nbsp;";
                        echo "Установить как Корп.";
                        echo "&nbsp;&nbsp;&nbsp;";
                        echo "Удалить";
                    }
                    echo "</p>";
                    // третья строка
                    echo "<p><small>";
                    echo "<span class='inblocktext'>Офис: <strong>".$groupact['office']."</strong></span><br />";
                    echo "<span class='inblocktext'>Дата начала: <strong>".$groupact['start_date']."</strong></span><br />";
                    echo "<span class='inblocktext'>Кто создал: <strong>".$groupact['creator']."</strong></span><br />";
                    echo "<span class='inblocktext'>Длительность занятия: <strong>".$groupact['duration']."</strong> ч.</span><br />";
                    echo "</small></p>";
                    // четвертая строка
                    echo "<p><small><span class='inblocktext'>Состав группы:</span> ";
                    foreach($groupact['sarr'] as $stdnt){
                        if($stdnt['visible']!=1){
                            echo "<s>";
                        }
                        echo Html::a($stdnt['sname'],['studname/view','id'=>$stdnt['stid']]);
                        if($stdnt['visible']!=1){
                            echo "</s>";
                        }
                        echo " ";
                    }
                    echo "</small></p>";
                    if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.uteacher')==$model->id){
                    // пятая строка
                    echo "<p><small>";
                    if($groupact['ltch']!=0){
                        echo "<span class='inblocktext'>Заданий на проверке: <strong.".$groupact['ltch']."</strong> зан.</span.<br/>";
                    }
                    if($groupact['htacc']!=0){
                        echo "<span class='inblocktext'>К начислению: <strong>".$groupact['htacc']."</strong> ч.</span><br />";
                    }
                    if($groupact['vless']!=0){
                        echo "<span class='inblocktext'>Всего проведено: <strong>".$groupact['vless']."</strong> ч.</span>";
                    }
                    echo "</small></p>";
                    }
                    echo "</div></div>";
                }
            }
            echo "</div>";
        }
        }
        // выводим информацию по начислениям
        if($tab == 3) {
        echo "<p></p>";
        if(!empty($accrualdates)){
        foreach($accrualdates as $key=>$accrualdate) {
            echo "<a href='#collapse-teacheraccruals-".$key."' role='button' data-toggle='collapse' aria-expanded='false' aria-controls='collapse-teacheraccruals-".$key."' class='text-warning'>Начисление от ".date('d.m.y', strtotime($accrualdate))."</a><br />";
            echo "<div class='collapse' id='collapse-teacheraccruals-".$key."'>";
            $total = 0;
            foreach($teacherdata as $accrual) {
                if($accrual['date']==$accrualdate) {
                    echo "<div class='panel panel-default'><div class='panel-body'>";
                    echo "<small>Начисление зарплаты #".$accrual['aid']." (за ".$accrual['hours']." ч. в группе #".$accrual['gid'].", ставка ".$accrual['tax']." р. на сумму ".round($accrual['value'])." р.";
					if(Yii::$app->session->get('user.ustatus')==3 || Yii::$app->session->get('user.ustatus')==8) {
						if(!$accrual['done']) {
							echo " " . Html::a('', ['accrual/doneaccrual', 'id'=>$accrual['aid'], 'type'=>'profile'], ['class'=>'glyphicon glyphicon-ok', 'title'=>'Выплатить начисление']);
							if(Yii::$app->session->get('user.ustatus')==3) {
					            echo " " . Html::a('', ['accrual/delaccrual', 'id'=>$accrual['aid']], ['class'=>'glyphicon glyphicon-trash', 'title'=>'Отменить начисление']);
						    }
						} else {
							if(Yii::$app->session->get('user.ustatus')==3) {
							    echo " " . Html::a('', ['accrual/undoneaccrual', 'id'=>$accrual['aid']], ['class'=>'glyphicon glyphicon-remove', 'title'=>'Отменить выплату']);
							}
						}
					}
					echo "</small><br />";
                    $total = $total + $accrual['value'];
                    echo "<small>Начислено: ".$accrual['create_date'].", ".$accrual['creator']."</small><br/>";
					if($accrual['done']) {
						echo "<small>Выплачено: " . $accrual['finish_date'] . ", " . $accrual['finisher'] . "</small>";
					}                    
                        echo "</div></div>";
                        }
                    }
                    echo "</div>";
                    echo "Всего: ".round($total)." р.<br />";
                }
            }
        }
        ?>
        </div>
        <!-- центральная область  -->
        <!-- правая боковая панель  -->
        <div id="right-sidebar" class="col-sm-2">
            <div class="panel panel-warning">
                <div class="panel-body">
                    <p>
                    <?= isset($model->user->logo) && $model->user->logo != '' ?
                        Html::img('@web/uploads/user/'.$model->user->id.'/logo/'.$model->user->logo, ['class'=>'thumbnail', 'width'=>'100%']) :
                        Html::img('@web/images/dream.jpg',['width'=>'100%'])
                    ?>
                    </p>
                    <?php if (!empty($lestocheck)) : ?>
                        <small><span class='inblocktext'>Занятий на проверке:</span> <span class='text-danger'><?= $lestocheck['cnt'] ?></span></small><br />
                    <?php endif; ?>
                    <?php if ((int)Yii::$app->session->get('user.ustatus') === 3 || (int)Yii::$app->session->get('user.uteacher') === (int)$model->id || (int)Yii::$app->session->get('user.uid') === 296) : ?>
                        <?php if (!empty($hourstoaccrual)) : ?>
                            <small><span class='inblocktext'>Часов к начислению:</span> <span class='text-danger'><?= ($hourstoaccrual['sm'] ? $hourstoaccrual['sm'] : 0) ?></span> <span class='inblocktext'>ч.</span></small><br />
                        <?php endif; ?>
                        <?php if ($sumaccrual > 0 && ((int)Yii::$app->session->get('user.ustatus') === 3 || (int)Yii::$app->session->get('user.uteacher') === (int)$model->id || (int)Yii::$app->session->get('user.uid') === 296)) : ?>
                            <small><span class='inblocktext'>Cумма к начислению:</span> <span class='text-danger'><?= $sumaccrual ?></span> <span class='inblocktext'>р.</span></small><br />
                        <?php endif; ?>
                        <?php if ($sum2pay['money'] > 0 && ((int)Yii::$app->session->get('user.ustatus') === 3 || (int)Yii::$app->session->get('user.uteacher') === (int)$model->id || (int)Yii::$app->session->get('user.uid') === 296)) : ?>
		                    <small><span class='inblocktext'>Cумма к выплате:</span> <span class='text-danger'><?= $sum2pay['money'] ?></span> <span class='inblocktext'>р.</span></small>
                        <?php endif; ?>
                    <?php endif; ?>      
                </div>
            </div>
            <?php if (!empty($unviewedlessons)) : ?>
                <?php foreach ($unviewedlessons as $uvl) : ?> 
                    <div class="panel panel-warning">
                        <div class="panel-body">
                        <?php 
                            // формируем текст ссылки на занятие
                            $link =  "<small>Занятие #".$uvl['jid']." в группе #".$uvl['gid']."</small>";
                            // распечатываем ссылку на занятие
                            echo Html::a($link, ['groupteacher/view','id'=>$uvl['gid'], '#'=>'lesson_'.$uvl['jid']]);
                            // уничтожаем ненужную переменную
                            unset($link);
                            echo "<br />";
                            // распечатываем дату и день занятие
                            echo "<small><span class='inblocktext'>".date('d.m.y', strtotime($uvl['lesdate']))." (".Yii::t('app', date('l', strtotime($uvl['lesdate']))).")</span></small><br />";
                            // офис
                            echo "<small><span class='inblocktext'>".$uvl['office']."</span></small><br />";
                            // количество активных студентво в группе и сколько из них присутствовало
                            echo "<small><span class='inblocktext'>Присутствовало ".$uvl['present']." из ".$uvl['all']."</span></small>";
                            // Для руководителей и менеджеров добавляем кнопку проверки занятия
                            if((int)Yii::$app->session->get('user.ustatus') === 3 ||
                               (int)Yii::$app->session->get('user.ustatus') === 4 ||
                               (int)Yii::$app->session->get('user.uid') === 296){
                                echo "<br/ ><small>";
                                echo Html::a('Так и есть :)', ['journalgroup/view','gid'=>$uvl['gid'],'id'=>$uvl['jid']]);
                                echo "</small>";
                            }
                        ?> 
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <!-- правая боковая панель -->
</div>
<!-- конец контент области -->
