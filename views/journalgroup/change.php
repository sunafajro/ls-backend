<?php

/**
 * @var yii\web\View           $this
 * @var yii\widgets\ActiveForm $form
 * @var array                  $checkTeachers
 * @var array                  $dates
 * @var array                  $groupInfo
 * @var array                  $history
 * @var array                  $items
 * @var array                  $params
 * @var array                  $statuses
 * @var array                  $students
 * @var string                 $userInfoBlock
 */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;

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
			<strong>дата добавления состава:</strong> <?= !empty($students) ? $students[0]['ldate'] : '' ?><br>
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
							foreach($statuses as $status){
								echo "<option";
								echo ($student['status']==$status['id']) ? " selected" : "";
								echo " value='" . $status['id'] . "'>";
								if ((int)$status['id'] === 3) {
									echo 'не было';
								} else if((int)$status['id'] === 2) {
									echo "не было (принес справку)";
								} else {
									echo $status['name'];
								}
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
	<?php
        foreach($dates as $key => $value){
			echo "<strong>".$value."</strong>";
			echo "<p>";
			foreach($history as $h){
				if($h['timestamp']==$key) {
				    echo $h['sname'].": <em><small>".$h['stname']."</small></em><br>";
				}
			}
			unset($h);
			echo "</p>";
		}
		unset($key);
		unset($value);
	?>
   </div>
</div>