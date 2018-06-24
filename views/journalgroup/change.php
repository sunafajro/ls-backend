<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CalcTeachergroup */
/* @var $form yii\widgets\ActiveForm */
//проверяем что идентификатор группы есть в get запросе и присваиваем его переменной $gid
if(Yii::$app->request->get('gid')&&Yii::$app->request->get('id')){
    $gid = (int)Yii::$app->request->get('gid');
    $lid = (int)Yii::$app->request->get('id');
}
else {
    $gid = 0;
    $lid = 0;
}
$this->title = Yii::t('app', 'Lesson members');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Group').' #'.$gid, 'url' => ['groupteacher/index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="calc-teachergroup-form">
    <nav class="navbar navbar-default">
    <div class="container-fluid">
        <?php
            echo Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> '.Yii::t('app','Add lesson'), ['groupteacher/addlesson','gid'=>$gid], ['class' => 'btn btn-default navbar-btn']);
            echo " ";
			echo Html::a(Yii::t('app','Journal'), ['groupteacher/view','id'=>$gid], ['class' => 'btn btn-default navbar-btn']);
			echo " ";
            echo Html::a(Yii::t('app','Students'), ['groupteacher/addstudent','gid'=>$gid], ['class' => 'btn btn-primary navbar-btn']);
            echo " ";
            echo Html::a(Yii::t('app','Teachers'), ['groupteacher/addteacher','gid'=>$gid], ['class' => 'btn btn-default navbar-btn']);
        ?>
    </div><!-- /.container-fluid -->
</nav>
<!--    <h4><?= Yii::t('app', 'Current members of the lesson').' #'.$gid ?></h4>
    <hr>
-->
    <h4><?= Yii::t('app', 'Change lesson members').' #'.$gid ?></h4>
    <hr>
<?php
    echo (!empty($students) ? "<p><strong>дата добавления состава:</strong> ".$students[0]['ldate'] : '');
    echo (!empty($students) ? "<br><strong>кто добавил состав:</strong> ".$students[0]['user'] : '');
?>
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
    <h4><?= Yii::t('app', 'Lesson members history').' #'.$gid ?></h4>
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