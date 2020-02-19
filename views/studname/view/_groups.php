<?php
	use yii\helpers\Html;
?>

<?php foreach($groups as $group) : ?>
	<?php $lessonsnum = 0; ?>
	<div class="panel panel-info">
	    <div class="panel-body">
            <p>
			<?=
                !$group['gvisible'] ?
                '<span class="text-info" title="Завершена">♥</span>' :  
				'<span class="text-danger" title="Активная"">♦</span>'
            ?>
			<?php
			$link2group = "группа#".$group['gid']." ".$group['sname']." (услуга#".$group['sid'].")";
			// для менеджеров, руководителей и преподавателей ведущих группу выводим ввиде ссылки
			if($group['tid']==Yii::$app->session->get('user.uteacher')||Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4){
				echo Html::a($link2group, ['groupteacher/view','id'=>$group['gid']]);
			} else {
				// для остальных просто текст
				echo $link2group;
			}
            ?>	
			</p>
			<p class="small">
			<b>Уровень:</b> <i><?= $group['elname'] ?></i>
			<br /><b>Преподаватель:</b> <i>
			<?php
			if($group['tid']==Yii::$app->session->get('user.uteacher')||Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4) {
				echo Html::a($group['tname'],['teacher/view','id'=>$group['tid']]);
			} else {
				// для остальных выводим ФИО как текст
				echo $group['tname'];
			}
            ?></i>
			<br /><b>Офис:</b> <i><?=$group['oname'] ?></i>
			<br /><b>Дата начала группы:</b> <i><?= date('d-m-Y', strtotime($group['gdate'])) ?></i>
            <br /><b>Статус группы:</b> <i>
			<?= ($group['gvisible'] == 1) ? 'Действующая' : 'Завершена' ?>
			<?= ($group['gvisible'] == 1) ? '' : ' ' . date('d-m-Y', strtotime($group['gvdate'])) ?></i>
			<br />
			<b>Зачислен в группу:</b> <?= date('d-m-Y', strtotime($group['sgdate'])) ?>
            <br /><b>Статус студента:</b> <i>
			<?= ($group['sgvisible'] == 1) ? 'в составе группы' : 'вышел из состава группы' ?>
			<?= ($group['sgvisible'] == 1) ? '' : ' ' . date("d-m-Y", strtotime($group['sgvdate'])) ?></i>
			<br /><b>Длительность занятия:</b> <i><?= $group['tnvalue'] ?> ч.</i></p>
			<?php
			if($group['tid'] == Yii::$app->session->get('user.uteacher') || Yii::$app->session->get('user.ustatus') == 3 || Yii::$app->session->get('user.ustatus') == 4) : ?>
				<p class="small"><b>Данные журнала:</b>
				<?php $tmpstatus = 0; ?>
				<?php foreach($lessons as $lesson) : ?>
					<?php if ($lesson['gid'] == $group['gid']) : ?>
						<?php if ($tmpstatus != $lesson['sjid']) : ?>
							<?php $tmpstatus = $lesson['sjid']; ?>
							<br /><span class="text-danger"><?= $lesson['sjname'] ?></span>
						<?php endif; ?>
						<?php if ($lesson['jgvisible'] == 1) : ?>
							(<span class="text-success"><?= date('d.m.y', strtotime($lesson['jgdate'])) ?></span>)
						<?php else : ?>
							(<del><?= date('d.m.y', strtotime($lesson['jgdate'])) ?></del>)
						<?php endif; ?>
						<?php if ($lesson['sjid'] != 2 && $lesson['jgvisible']) {
							$lessonsnum++;
						} ?>
					<?php endif; ?>
				<?php endforeach; ?>
				<br /><b>Количество занятий:</b> <i><?= $lessonsnum ?> зан.</i>
				<br /><b>Расписание занятий:</b>
                <?php foreach($schedule as $s) : ?>
                    <?php if ($s['group_id'] == $group['gid']) :?>
                    <span class="label label-info"><?= $s['day'] ?></span>
                    <?php endif; ?>
                <?php endforeach; ?>
			    </p>
			<?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>