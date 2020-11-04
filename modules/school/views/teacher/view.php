<?php

/**
 * @var View    $this
 * @var Teacher $model
 * @var float   $accrualSum
 * @var array   $efm
 * @var array   $hoursToAccrual
 * @var array   $jobPlace
 * @var array   $lessonsToCheck
 * @var float   $sum2pay
 * @var array   $teacherData
 * @var array   $teacherSchedule
 * @var array   $unViewedLessons
 * @var string  $userInfoBlock
 * @var array   $viewedLessons
 */

use app\modules\school\assets\ChangeGroupParamsAsset;
use app\models\Teacher;
use app\modules\school\assets\TeacherViewAsset;
use app\widgets\alert\AlertWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

ChangeGroupParamsAsset::register($this);
TeacherViewAsset::register($this);

$this->title = Yii::$app->params['appTitle'] . $model->name;

$roleId    = (int)Yii::$app->user->identity->roleId;
$teacherId = (int)Yii::$app->user->identity->teacherId;
$userId    = (int)Yii::$app->user->identity->id;

if ($roleId !== 5) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app','Teachers'), 'url' => ['index']];
} else {
    $this->params['breadcrumbs'][] = Yii::t('app','Teachers');
}
$this->params['breadcrumbs'][] = $model->name;

//формируем массив со списком названий дней недели
for ($i=0; $i<7; $i++) {
    $days[date('N', strtotime('+'.$i.' day'))] = date('D', strtotime('+'.$i.' day'));
}
ksort($days);
// формируем список дней в которые есть занятия по расписанию
$i = 0;
foreach ($teacherSchedule as $sched)    {
    $sscheddays[$i] = $sched['day'];
    $i++;
}
//если нет занятий в расписании, создаем пустой массив
if (empty($sscheddays)) {
    $sscheddays[0] = 0;
}
// оставляем только уникальные значения
$scheddays = array_unique($sscheddays);
// сортируем по порядку
ksort($scheddays);
// проверяем какие данные выводить в карочку преподавателя: 1/2 - группы, 3 - начисления; 4 - выплаты фонда
if (Yii::$app->request->get('tab')) {
    $tab = Yii::$app->request->get('tab');
} else {
    if (Yii::$app->session->get('user.ustatus') == 8) {
        $tab = 3;
    } else {
        $tab = 1;
    }	
}
$accrualDates = [];
// выбираем даты начислений
if ((int)$tab === 3) {
    if (!empty($teacherData)) {
        $accrualDates = ArrayHelper::getColumn($teacherData, 'date');
        $accrualDates = array_unique($accrualDates);
        rsort($accrualDates);
    }
}
?>
<!-- начало контент области -->
<div class="row row-offcanvas row-offcanvas-left teacher-view">
    <!-- левая боковая панель -->
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?>
        <?php if (in_array($roleId, [3, 4]) || $userId === 296) { ?>
            <h4><?= Yii::t('app', 'Actions') ?>:</h4>
            <?php if ($userId !== 296) { ?>
              <?= Html::a('<span class="fa fa-users" aria-hidden="true"></span> ' . Yii::t('app', 'Add group'), ['groupteacher/create','tid'=>$model->id], ['class' => 'btn btn-default btn-sm btn-block', 'title' => Yii::t('app','Add group')]) ?>
            <?php } ?>
            <?= Html::a('<span class="fa fa-language" aria-hidden="true"></span> ' . Yii::t('app', 'Add language'), ['langteacher/create','tid'=>$model->id], ['class' => 'btn btn-default btn-sm btn-block', 'title' => Yii::t('app','Add language')]) ?>
            <?php if ($roleId !== 4) { ?>
                <?= Html::a('<span class="fa fa-money" aria-hidden="true"></span> ' . Yii::t('app', 'Add rate'), ['edunormteacher/create','tid'=>$model->id], ['class' => 'btn btn-default btn-sm btn-block', 'title' => Yii::t('app','Add rate')]) ?>
            <?php } ?>
            <?php if ($userId !== 296 && $roleId !== 4) { ?>
            <?= Html::a('<span class="fa fa-gift" aria-hidden="true"></span> ' . Yii::t('app', 'Add premium'), ['teacher/language-premiums','tid'=>$model->id], ['class' => 'btn btn-default btn-sm btn-block', 'title' => Yii::t('app','Add premium')]) ?>
            <?php } ?>
            <?= Html::a('<span class="fa fa-pencil" aria-hidden="true"></span> ' . Yii::t('app', 'Edit'), ['teacher/update','id'=>$model->id], ['class' => 'btn btn-warning btn-sm btn-block', 'title' => Yii::t('app','Edit teacher')]) ?>
            <?php if ($userId !== 296 && $roleId !== 4) { ?>
                <?= Html::a('<span class="fa fa-trash" aria-hidden="true"></span> ' . Yii::t('app', 'Delete'), ['teacher/delete','id'=>$model->id], ['class' => 'btn btn-danger btn-sm btn-block', 'title' => Yii::t('app','Delete teacher')]) ?>
            <?php } ?>
        <?php } ?>
    </div>
    <!-- левая боковая панель -->
    <!-- центральная область -->
    <div id="content" class="col-sm-8">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
        ]); ?>
        <?php endif; ?>
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
        <?= AlertWidget::widget() ?>
        <h4>
            <?= isset($model->name) && $model->name != '' ? $model->name : '' ?>
            <?= isset($model->birthdate) && $model->birthdate != '' ? ' :: ' . date('d.m.y', strtotime($model->birthdate)) : '' ?>
            <?= isset($model->phone) && $model->phone != '' ? ' :: ' . $model->phone : '' ?>
            <?= isset($model->email) && $model->email != '' ? ' :: ' . $model->email : '' ?>
            <?= isset($model->social_link) && $model->social_link != '' ? ' :: ' . Html::a('', 'https://'.$model->social_link, ['class'=>'glyphicon glyphicon-new-window', 'target'=>'_blank', 'title'=>Yii::t('app', 'Link to social profile')]) : '' ?>
            <?php
                if (!empty($teacherTax)) {
                  $places = []; 
                  foreach($teacherTax as $tax) {
                    $str  = '<span class="label ' . ((int)$tax['tjplace'] === 1 ? 'label-success' : 'label-info') . '">';
                    $str .= $jobPlace[$tax['tjplace']] . '</span>';
                    $places[$tax['tjplace']] = $str;
                  }
                  ksort($places);
                  echo ' :: ' . implode(' / ', $places);
                }
            ?>
        </h4>
        <?= $model->address ? '<p><b>' . Yii::t('app', 'Address') . ':</b> <i>' . $model->address . '</i></p>' : '' ?>
        <?php $ttax = []; ?>
        <?php if (!empty($teacherTax)) { ?>
            <div style="margin-bottom:0.5rem">
                <?php if ($roleId === 3 || $teacherId === (int)$model->id || $userId === 296) { ?>
                    <?php foreach($teacherTax as $tax) { ?>
                        <div style="margin-bottom:0.2rem">
                            <strong>Ставка преподавателя:</strong> <?= $tax['taxname'] ?> <small>
                                (<i>
                                    <span class='inblocktext'>назначена  <?= date('d.m.y', strtotime($tax['taxdate'])) ?></span>
                                </i>
                                <span class="label <?= ((int)$tax['tjplace'] === 1 ? 'label-success' : 'label-info') ?>"><?= $jobPlace[$tax['tjplace']] ?></span>
                                <?php $ttax[(int)$tax['tjplace']] = $tax['taxvalue']; ?>)
                            </small>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        <?php } ?>
        <div>
            <a href='#collapse-schedule' role='button' data-toggle='collapse' aria-expanded='false' aria-controls='collapse-schedule' class='text-warning'>показать/скрыть расписание занятий</a>
            <div class="collapse" id="collapse-schedule">
                <?php
                foreach ($days as $key => $value) {
                    if (in_array($key, $scheddays)) {
                        echo "<p><strong>" . Yii::t('app', $value) . "</strong></br>";
                        foreach($teacherSchedule as $schedule){
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
        </div>
        <div>
            <?php
                // блок со списком проверенны занятий
                if ($roleId === 3 || $userId === 296 || $teacherId === (int)$model->id) {
                    echo $this->render('_viewed_lessons', [
                        'viewedLessons' => $viewedLessons,
                    ]);
                } ?>
        </div>
        <!-- блок с табами -->
        <ul class="nav nav-tabs" style="margin-top: 1rem; margin-bottom: 1rem">
            <?php if (in_array($roleId, [3, 4, 6]) || $teacherId === (int)$model->id) { ?>
                <li role="presentation" class="<?= ((int)$tab === 1 ? 'active' : '') ?>">
                    <?= Html::a(Yii::t('app','Active groups'), ['teacher/view', 'id' => $model->id, 'tab' => 1]) ?>
                </li>
                <li role="presentation" class="<?= ((int)$tab === 2 ? 'active' : '') ?>">
                    <?= Html::a(Yii::t('app','Finished groups'), ['teacher/view', 'id' => $model->id, 'tab' => 2]) ?>
                </li>
            <?php } ?>
            <?php if (in_array($roleId, [3, 8]) || $teacherId === (int)$model->id || $userId === 296) { ?>
                <li role="presentation" class="<?= ((int)$tab === 3 ? 'active' : '') ?>">
                    <?= Html::a(Yii::t('app','Accruals'), ['teacher/view', 'id' => $model->id, 'tab' => 3]) ?>
                </li>
            <?php } ?>
	    </ul>
        <!-- блок с табами -->
       <?php
            // выводим информаию о группах
            if($tab == 1 || $tab == 2) {
                // задаем форму обучения. 1 - индивидуальные, 2 - группа, 3 - минигруппа, 4 - без привязки
                $eduform = [0, 1, 3, 2, 4];
                // делаем цикл в три шага для вывода групп по формам обучения
                for($a=1; $a<=4; $a++) {
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
                    foreach($teacherData as $groupact) {
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
                            if ($roleId === 3 || $roleId === 4){
                                echo "&nbsp;&nbsp;&nbsp;";
                                // выводим ссылку на завершение
                                echo Html::a((int)$groupact['visible'] == 1 ? Yii::t('app','Finish') : Yii::t('app','To active'), ['groupteacher/status', 'id' => $groupact['gid'], 'lid' => $model->id]);
                                echo "&nbsp;&nbsp;&nbsp;";
                                // выводим ссылку на изменение типа группы
                                echo Html::a(
                                    Yii::t('app', (int)$groupact['corp'] === 1 ? 'Make normal' : 'Make corporative'),
                                    'javascript:void(0)',
                                    [
                                        'class' => 'js--change-group-params-btn',
                                        'data-url' => Url::to(['groupteacher/change-params', 'id' => $groupact['gid'], 'name' => 'corp', 'value' => (int)$groupact['corp'] === 1 ? '0' : '1'])
                                    ]
                                );
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
                            if ($roleId === 3 || $teacherId === (int)$model->id){
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
            if ((int)$tab === 3) {
                echo $this->render('_accruals', [
                    'accrualDates' => $accrualDates,
                    'roleId'       => $roleId,
                    'teacherData'  => $teacherData,
                ]);
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
                    <?php if (!empty($lessonsToCheck)) { ?>
                        <small><span class='inblocktext'>Занятий на проверке:</span> <span class='text-danger'><?= $lessonsToCheck['cnt'] ?></span></small><br />
                    <?php } ?>
                    <?php if ($roleId === 3 || $teacherId === (int)$model->id || $userId === 296) : ?>
                        <?php if (!empty($hoursToAccrual)) { ?>
                            <small><span class='inblocktext'>Часов к начислению:</span> <span class='text-danger'><?= ($hoursToAccrual['sm'] ? $hoursToAccrual['sm'] : 0) ?></span> <span class='inblocktext'>ч.</span></small><br />
                        <?php } ?>
                        <?php if ($accrualSum > 0 && ($roleId === 3 || $teacherId === (int)$model->id || $userId === 296)) : ?>
                            <small><span class='inblocktext'>Cумма к начислению:</span> <span class='text-danger'><?= $accrualSum ?></span> <span class='inblocktext'>р.</span></small><br />
                        <?php endif; ?>
                        <?php if ($sum2pay['money'] > 0 && ($roleId === 3 || $teacherId === (int)$model->id || $userId === 296)) : ?>
		                    <small><span class='inblocktext'>Cумма к выплате:</span> <span class='text-danger'><?= $sum2pay['money'] ?></span> <span class='inblocktext'>р.</span></small>
                        <?php endif; ?>
                    <?php endif; ?>      
                </div>
            </div>
            <?php if (!empty($unViewedLessons)) { ?>
                <?php foreach ($unViewedLessons as $uvl) { ?>
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
                                if ($roleId === 3 || $roleId === 4 || $userId === 296){
                                    echo "<br/ ><small>";
                                    echo Html::a('Так и есть :)', ['journalgroup/view','gid'=>$uvl['gid'],'id'=>$uvl['jid']]);
                                    echo "</small>";
                                }
                            ?>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
    <!-- правая боковая панель -->
</div>
<!-- конец контент области -->
