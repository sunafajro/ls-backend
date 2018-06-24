<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model app\models\CalcGroupteacher */

$this->title = "Система учета :: Группа №" . $model->id;
$this->params['breadcrumbs'][] = Yii::t('app','Group') . ' №' . $model->id;
$this->params['breadcrumbs'][] = Yii::t('app', 'Journal');
?>

<div class="row row-offcanvas row-offcanvas-left group-index">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= $userInfoBlock ?>
        <?php if($model->visible == 1): ?>
            <?php
                if((int)Yii::$app->session->get('user.ustatus') === 3 ||
                   (int)Yii::$app->session->get('user.ustatus') === 4 ||
                   (int)Yii::$app->session->get('user.uid') === 296 ||
                   (int)Yii::$app->session->get('user.ustatus') === 10 ||
                   array_key_exists(Yii::$app->session->get('user.uteacher'), $check_teachers)) : ?>
                   <?= Html::a('<span class="fa fa-plus" aria-hidden="true"></span> '.Yii::t('app','Add lesson'), ['journalgroup/create','gid' => $model->id], ['class' => 'btn btn-default btn-block']) ?>
            <?php endif; ?>
            <?php foreach($items as $item): ?>
                <?= Html::a($item['title'], $item['url'], $item['options']) ?>
            <?php endforeach; ?>
        <?php endif; ?>
        <h4><?= Yii::t('app', 'Filters') ?>:</h4>
        <?php 
            $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['groupteacher/view', 'id'=>$model->id],
                ]);
        ?>
        <div class="form-group">
            <select class="form-control input-sm" name="status">
                <option value='all'><?= Yii::t('app','-all states-') ?></option>
                <option value="1" <?= $state == 1 ? ' selected' : '' ?>>На проверке</option>
                <option value="2" <?= $state == 2 ? ' selected' : '' ?>>Проверено</option>
                <option value="3" <?= $state == 3 ? ' selected' : '' ?>>Оплачено</option>
                <option value="4" <?= $state == 4 ? ' selected' : '' ?>>Исключено</option>
            </select>
        </div>
        <div class="form-group">
            <?= Html::submitButton('<span class="fa fa-filter" aria-hidden="true"></span> ' . Yii::t('app', 'Apply'), ['class' => 'btn btn-info btn-sm btn-block']) ?>
        </div>
        <?php ActiveForm::end(); ?>
        <h4>Параметры группы №<?= $model->id; ?></h4>
		<div class="well well-sm">
		<?php $i = 0; ?>
        <?php foreach($groupinfo as $key => $value): ?>
		    <?php if($i != 0): ?>
			<br>
            <?php endif; ?>
            <span class="small"><b><?= $key ?>:</b></span> <span class="text-muted small"><?= $value ?></span>
			<?php $i++; ?>
        <?php endforeach; ?>
	    </div>
    </div>
	<div class="col-sm-10">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
		<?php if(Yii::$app->session->hasFlash('error')): ?>
			<div class="alert alert-danger" role="alert">
				<?= Yii::$app->session->getFlash('error') ?>
			</div>
		<?php endif; ?>

		<?php if(Yii::$app->session->hasFlash('success')): ?>
			<div class="alert alert-success" role="alert">
				<?= Yii::$app->session->getFlash('success') ?>
			</div>
		<?php endif; ?>

        <?php
        echo "<h4>Журнал группы №" . $model->id . "</h4>";
        // блок паджинации
        echo "<nav>";
        echo "<ul class='pager'>";
        echo "<li class='previous'>".(($page > 1 && (($page-1)*5 < $pages->totalCount)) ? Html::a('Предыдущий', ['groupteacher/view', 'id' => $model->id, 'status' => $state ? $state : 'all', 'page'=>($page-1)]) : '')."</li>";
        echo "<li class='next'>".(($page*5 < $pages->totalCount) ? Html::a('Следующий', ['groupteacher/view', 'id' => $model->id, 'status' => $state ? $state : 'all', 'page' => ($page+1)]) : '')."</li>";
        echo "</ul>";
        echo "</nav>";

    // распечатываем записи о занятиях
    foreach($lessons as $lesson){
        if($lesson['jview'] == 0 && $lesson['jdone'] == 0 && $lesson['jvisible'] == 1) {
            $color = 'warning';
        } elseif ($lesson['jview'] == 1 && $lesson['jdone'] == 0 && $lesson['jvisible'] == 1) {
            $color = 'info';
        } elseif($lesson['jview'] == 1 && $lesson['jdone'] == 1 && $lesson['jvisible'] == 1) {
            $color = 'success';
        } elseif($lesson['jvisible'] == 0) {
            $color = 'danger';
        } else {
            $color = 'default';
        }
        echo "<div class='panel panel-" . $color . "'>";
        //echo ($lesson['jvisible']==1) ? "primary'>" : "default'>";
        echo "<div class='panel-heading'>";
        echo "<a name='lesson_".$lesson['jid']."'></a>";
        switch($lesson['edutime']){
                case 1: echo Html::img('/images/day.png',['title'=>Yii::t('app','Work time')]);break;
                case 2: echo Html::img('/images/night.png',['title'=>Yii::t('app','Evening time')]);break;
                case 3: echo Html::img('/images/halfday.png',['title'=>Yii::t('app','Halfday time')]);break;
                }
        if($lesson['jview']==1){
        echo " <span class='text-success' title='".Yii::t('app','Lesson viewed')."'>&#10003;</span>";
        if($lesson['jdone']==0){
            echo " <span class='text-danger' title='".Yii::t('app','Lesson undone')."'>&diams;</span>";
        } else { echo " <span class='text-info' title='".Yii::t('app','Lesson done')."'>&hearts;</span>";
        }
        }
        echo ($lesson['jvisible']!=1) ? " <del>" : "";
        echo " Занятие #".$lesson['jid']." от ".date('d-m-Y', strtotime($lesson['jdate']))." (".Yii::t('app',date("l",strtotime($lesson['jdate']))).")";
        echo ($lesson['visible_date']!='0000-00-00') ? "</del> " : " ";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        if($lesson['jvisible']==1){
            if($lesson['jdone']!=1){
                if($lesson['jview']==0){
                    // занятие могут отредактировать только  преподаватель назначенный в группу, менеджер или руководитель
                    if((int)Yii::$app->session->get('user.ustatus') === 3 ||
                       (int)Yii::$app->session->get('user.ustatus') === 4 ||
                       (int)Yii::$app->session->get('user.ustatus') === 10 ||
                       array_key_exists(Yii::$app->session->get('user.uteacher'), $check_teachers)) {
                        echo Html::a(Yii::t('app','Edit'), ['journalgroup/update', 'id'=>$lesson['jid'], 'gid'=>$model->id]);
                    }
                    // проверить занятие могут только менеджер или руководитель
                    if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4){
                        echo " | " . Html::a("Так и есть :)",['journalgroup/view','id'=>$lesson['jid'], 'gid'=>$model->id]);
                    }
                }
                // отменить проверку занятия могут только менеджер или руководитель
                elseif($lesson['jview']==1&&(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4)) {
                    echo " | " . Html::a("Отменить 'проверено'",['journalgroup/unview','gid'=>$model->id,'id'=>$lesson['jid']]);
                }
                // занятие могут исключить только преподаватель назначенный в группу, менеджер или руководитель
                if((int)Yii::$app->session->get('user.ustatus') === 3 ||
                   (int)Yii::$app->session->get('user.ustatus') === 4 ||
                   (int)Yii::$app->session->get('user.ustatus') === 10 ||
                   array_key_exists(Yii::$app->session->get('user.uteacher'), $check_teachers)) {
                        echo " | " . Html::a("Исключить из журнала",['journalgroup/delete','gid'=>$model->id,'id'=>$lesson['jid']]);
                }
            }
        }
        else {
            echo Html::a("Восстановить в журнал",['journalgroup/restore','gid'=>$model->id,'id'=>$lesson['jid']]);
            if(Yii::$app->session->get('user.ustatus')==3&&Yii::$app->session->get('user.ustatus')==4){
                " | ".Html::a("Удалить",['journalgroup/remove','gid'=>$model->id,'id'=>$lesson['jid']]);
            }
        }
        echo "</div>";
        echo "<div class='panel-body'>";
        echo "<p><strong>Описание:</strong> <br />".$lesson['jdesc']."</p>";
        echo "<p><strong>Д/з:</strong> <br />".$lesson['jhwork']."</p>";
        echo "<p><small><strong>Преподаватель:</strong> ".$lesson['tname']."</small><br />";
        echo "<small><span class='text-warning'>Кем добавлено: ".$lesson['uname']."</small></span>";
        // выводим состояние занятия
        if($lesson['jvisible']==1) { 
        echo"<br /><span class='text-primary'><small>Статус: ";
        if($lesson['jdone']==1){
            echo "Начисление #".$lesson['accrual'];
            echo ", когда и кем: ".date('d-m-Y',strtotime($lesson['done_date'])).", ".$lesson['jduser'];
            }
        else{
            if($lesson['jview']==1){ echo "ожидает начисления";}
            else{echo "ожидает проверки";}
            }
        echo "</small></span>";
        }
        // выводим информацию о проверке занятия
        if($lesson['view_date']!='0000-00-00'){
        echo "<br /><span class='text-info'><small>";
        if($lesson['jview']==1){
            echo "Запись о занятии успешно проверена: ";
        }
        else{
            echo "Запись о занятии снова на проверке: ";
        }
        echo date('d-m-Y', strtotime($lesson['view_date'])).", кем: ".$lesson['view_user']."</small></span>";
        }
        // выводим информацияю о исключении-восстановлении занятия
        if($lesson['visible_date']!='0000-00-00'){
            echo "<br /><span class='text-danger'><small>";
            if($lesson['jvisible']!=1) { 
                echo "Запись о занятии исключена из журнала: ";
            } else { 
                echo "Запись о занятии восстановлена в журнал: ";
            }
            echo date('d-m-Y', strtotime($lesson['visible_date'])).", ";
            echo "кем: ".$lesson['jvuser']."</small></span>";
        }
        if($lesson['edit_date']!='0000-00-00'){
            echo "<br /><span class='text-success'><small>";
            echo "Состав занятия отредактирован: ".date('d-m-Y', strtotime($lesson['edit_date'])).", кем: ".$lesson['jeuser']."</span></p></small>";
        }
        echo "<p>";
        // для руководителей и менеджеров выводим ссылку на редактирование состава занятия
        if((Yii::$app->session->get('user.ustatus')==4||Yii::$app->session->get('user.ustatus')==3)&&$lesson['jview']!=1){
            echo "<small>".Html::a("Редактировать состав занятия #".$lesson['jid'], ['journalgroup/change','id'=>$lesson['jid'], 'gid'=>$model->id])."</small><br/>";
        }
        // проверяем есть ли массив со списком присутствовавших студентов для занятия
        if(isset($lesattend[$lesson['jid']]['id'])){
            // сверяем идентификаторы занятия между двумя массивами
        if($lesattend[$lesson['jid']]['id']==$lesson['jid'] && isset($lesattend[$lesson['jid']]['p'])){
            echo "<small>присутствовал: ";
                // распечатываем массив студентов
            foreach($students as $student){
                // проверяем что студент присутствовал на занятии
                if($student['jid']==$lesson['jid']&&$student['status']==1){
                        echo " (".Html::a($student['sname'],['studname/view','id'=>$student['sid']]).")";                  
                }
            }
        echo "</small>";
        }}
        if(isset($lesattend[$lesson['jid']]['id'])){
        if($lesattend[$lesson['jid']]['id']==$lesson['jid'] && isset($lesattend[$lesson['jid']]['a1'])){
            echo "<br/><small>не было (принес справку): ";
            foreach($students as $student){
                if($student['jid']==$lesson['jid']&&$student['status']==2){
                echo " (".Html::a($student['sname'],['studname/view','id'=>$student['sid']]).")";
                }
            }
        echo "</small>";
        }}
        // распечатываем отсутствоваших не предупредивших
        if(isset($lesattend[$lesson['jid']]['id'])){
        if($lesattend[$lesson['jid']]['id']==$lesson['jid'] && isset($lesattend[$lesson['jid']]['a2'])){
            echo "<br /><small>не было: ";
            foreach($students as $student){
                if($student['jid']==$lesson['jid'] && $student['status']==3){
                echo ' (' . Html::a($student['sname'],['studname/view','id'=>$student['sid']]);
                if(((int)Yii::$app->session->get('user.ustatus') === 4 || (int)Yii::$app->session->get('user.ustatus') === 3) && (int)$lesson['jview'] === 1) {
                echo ' ' . Html::a('<i class="fa fa-times" aria-hidden="true"></i>', ['journalgroup/absent', 'jid' => $lesson['jid'], 'sid' => $student['sid'], 'gid' => $model->id], ['title' => Yii::t('app', 'To absent (was ill)')]);
                }
                echo  ')';
                }
            }
        echo "</small>";
        }}
        echo "</p>";
        echo "</div>";
        echo "</div>";
    }
    ?>
<?php
    // блок паджинации
    echo "<nav>";
    echo "<ul class='pager'>";
    echo "<li class='previous'>".(($page > 1 && (($page-1)*5 < $pages->totalCount)) ? Html::a('Предыдущий', ['groupteacher/view', 'id' => $model->id, 'status' => $state ? $state : 'all', 'page'=>($page-1)]) : '')."</li>";
    echo "<li class='next'>".(($page*5 < $pages->totalCount) ? Html::a('Следующий', ['groupteacher/view', 'id' => $model->id, 'status' => $state ? $state : 'all', 'page' => ($page+1)]) : '')."</li>";
    echo "</ul>";
    echo "</nav>";

?>
    </div>
</div>
