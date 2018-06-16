<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CalcGroupteacherSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \Yii::t('app','Groups');
$this->params['breadcrumbs'][] = $this->title;

//составляем список преподавателей для селектов
foreach($steachers as $steacher){
$tempteachers[$steacher['tid']]=$steacher['tname'];
}
$teachers = array_unique($tempteachers);

//составляем список языков для селектов
foreach($teacherlangs as $lang){
$templangs[$lang['lid']]=$lang['lname'];
}
$slangs = array_unique($templangs);

//обрабатываем GET
//$oid = Yii::$app->request->get('OID') ? Yii::$app->request->get('oid') : NULL;
$lid = Yii::$app->request->get('LID') ? Yii::$app->request->get('LID') : NULL;
//$eid = Yii::$app->request->get('EID') ? Yii::$app->request->get('eid') : NULL;
//$aid = Yii::$app->request->get('AID') ? Yii::$app->request->get('aid') : NULL;
$tid = Yii::$app->request->get('TID') ? Yii::$app->request->get('TID') : NULL;

?>
<div class="calc-groupteacher-index">

<nav class="navbar navbar-default">
    <div class="container-fluid">
        <?= Html::a("<span class='glyphicon glyphicon-plus' aria-hidden='true'></span>", ['create'], ['class' => 'btn btn-default navbar-btn']) ?>
        <?php 
                $form = ActiveForm::begin([
                    'method' => 'get',
                    'options' => ['class' => 'navbar-form navbar-right'],
                    'action' => 'index.php?r=groupteacher/index',
                    ]);
                    ?>
	    <div class="form-group">
		 <select class="form-control input-sm" name="LID">
                        <option value="all">-все языки-</option>
                    <?php
                        // распечатываем список языков в селект
                        foreach($slangs as $lkey => $slang){
                            echo "<option ";
                            if($lkey==$lid){ echo "selected";}
                            echo " value='".$lkey."'>".mb_substr($slang,0,13,'UTF-8')."</option>";
                        }
                    ?>
                    </select>

		<select class="form-control input-sm" name="TID">
                        <option value="all">-все преподаватели-</option>
                    <?php 
                        foreach($teachers as $tkey => $teacher){
                            echo "<option ";
                            if($tkey==$tid){ echo "selected";}
                            echo " value='".$tkey."'>".mb_substr($teacher,0,22,'UTF-8')."</option>";
                        }
                    ?>
                    </select>
	    </div>
	    <button type="submit" class="btn btn-default btn-sm">GO</button>
        <?php ActiveForm::end(); ?>
    </div><!-- /.container-fluid -->
</nav>


    <?php
	echo "<table class='table table-stripped table-bordered'>";
        echo "<thead>";
        echo "<tr><th>Услуга</th><th>Уровень</th><th>Преподаватель</th><th>Офис</th><th>Дата</th><th>Кол-во</th><th>Действия</th></tr>";
        echo "</thead>";
        foreach($groups as $group)
        {
        echo "<tr>";
        echo "<td>".Html::a($group['sname'],['groupteacher/view','id'=>$group['gid']])."</td><td>".$group['ename']."</td>";
	echo "<td>".Html::a($group['tname'],['teacher/view','id'=>$group['tid']])."</td><td>".$group['oname']."</td><td>".date('d-m-Y', strtotime($group['sdate']))."</td>";
	echo "<td>";
	foreach($pupils as $pupil){
	if($pupil['gid']==$group['gid']){echo $pupil['pcount'];}
	}
	echo "</td>";
        echo "<td><span class='glyphicon glyphicon-pencil'></span></a></td>";
        echo "</tr>";
        } //end of foreach statement
        echo "</table>";
    ?>

</div>
