<?php

use app\models\Journalgroup;
use app\widgets\Alert;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use yii\web\View;

/**
 * @var View       $this
 * @var ActiveForm $form
 * @var array      $checkTeachers
 * @var array      $dates
 * @var array      $groupInfo
 * @var array      $history
 * @var array      $items
 * @var array      $params
 * @var array      $students
 * @var string     $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Edit lesson');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Group').' №' . $params['gid'], 'url' => ['groupteacher/view', 'id' => $params['gid']]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edit lesson');
?>
<div class="row row-offcanvas row-offcanvas-left journalgroup-change">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
	    <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
		<?php } ?>
		<?= $userInfoBlock ?>
		<?php if($params['active'] == 1) { ?>
			<?php if(
                    (int)Yii::$app->session->get('user.ustatus') === 3 ||
                    (int)Yii::$app->session->get('user.ustatus') === 4 ||
                    (int)Yii::$app->session->get('user.uid') === 296 ||
                    array_key_exists(Yii::$app->session->get('user.uteacher'), $checkTeachers)) { ?>
                <?= Html::a('<span class="fa fa-plus" aria-hidden="true"></span> ' . Yii::t('app','Edit lesson'), ['journalgroup/change','gid' => $params['gid'], 'id' => $params['lid']], ['class' => 'btn btn-block btn-primary']) ?>
            <?php } ?>
			<?php foreach($items as $item) { ?>
				<?= Html::a($item['title'], $item['url'], $item['options']) ?>
			<?php } ?>
		<?php } ?> 
		<h4>Параметры группы №<?= $params['gid']; ?></h4>
		<div class="well well-sm">
		<?php $i = 0; ?>
        <?php foreach($groupInfo as $key => $value): ?>
		    <?php if($i != 0): ?>
			<br>
            <?php endif; ?>			
            <span class="small"><b><?= $key ?>:</b></span> <span class="text-muted small"><?= $value ?></span>
			<?php $i++; ?>
        <?php endforeach; ?>
	    </div>
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
		<h4><?= Yii::t('app', 'Change lesson members') . ' #' . $params['gid'] ?></h4>
		<hr>
		<p>
			<strong>дата добавления состава:</strong> <?= !empty($students) ? date('d.m.Y', strtotime($students[0]['ldate'])) : '' ?><br>
			<strong>кто добавил состав:</strong> <?= !empty($students) ? $students[0]['user'] : '' ?></p>
		</p>
    	<?php $form = ActiveForm::begin(); ?>

    <?php
	if(!empty($students)) {
		foreach($students as $student){
			echo "<div class='form-group field-studjournalgroup-student_".$student['sid']."'>";
				echo "<label class='control-label' for='studjournalgroup-comment_".$student['sid']."'>".$student['sname']."</label>";
				echo "<div class='row'>";
					echo "<div class='col-sm-6'>";
						echo "<input type='text' id='studjournalgroup-comment_".$student['sid']."' class='form-control' name='Studjournalgroup[comment_".$student['sid']."]' value='".$student['comment']."'>";
				    echo "</div>";
				    echo "<div class='col-sm-6'>";
						echo "<select class='form-control' id='studjournalgroup-status_".$student['sid']."' name='Studjournalgroup[status_".$student['sid']."]'>";
							foreach(Journalgroup::getAttendanceAllStatuses() as $key => $status){
								echo "<option";
								echo ((int)$student['status'] === $key) ? " selected" : "";
								echo " value='" . $key . "'>";
								echo $status;
								echo "</option>";
							}
						echo "</select>";
				    echo "</div>";
		        echo "</div>";
		    echo "</div>";

		}
		echo Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary']);
	}
    ?>

    <?php ActiveForm::end(); ?>
    <h4><?= Yii::t('app', 'Lesson members history') . ' #' . $params['gid'] ?></h4>
    <hr>
	<?php foreach ($dates as $key => $value) { ?>
		<strong><?= date('d.m.Y', strtotime($value)) ?></strong>
		<p>
		<?php foreach($history as $h) { ?>
			<?php if ($h['timestamp']==$key) { ?>
				<?= $h['sname'] ?>: <em><small><?= $h['stname'] ?></small></em><br>
			<?php } ?>
		<?php } ?>
		</p>
	<?php } ?>
   </div>
</div>