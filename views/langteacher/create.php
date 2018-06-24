<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CalcLangteacher */

$this->title = Yii::t('app','Teacher languages');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Teachers'), 'url' => ['teacher/index']];
$this->params['breadcrumbs'][] = ['label' => $teacher['tname'], 'url' => ['teacher/view','id'=>$teacher['tid']]];
$this->params['breadcrumbs'][] = $this->title;

//составляем список преподавателей для селекта
foreach($slangs as $slang){
    $langs[$slang['lid']]=$slang['lname'];
}
foreach($tlangs as $tlang){
while(array_search($tlang['lname'], $langs)){
$key = array_search($tlang['lname'], $langs);
unset($langs[$key]);
}
}
?>
<div class="calc-langteacher-create">

    <!--<h1><?php /*Html::encode($this->title);*/ ?></h1>-->
<?php
    if($tlangs != 0){
    echo "<h4>Список языков преподавателя « ".$teacher['tname']." »</h4>";
    echo "<hr>";

    foreach($tlangs as $tlang){
    echo "<p>";
    echo "<strong>".$tlang['lname']."</strong> ";
    echo Html::a("<span class='glyphicon glyphicon-remove' aria-hidden='true'></span>",['langteacher/disable','id'=>$tlang['clid'],'tid'=>$teacher['tid']]);
    echo "<br>кем добавлен: ".$tlang['uname'];
    echo "<br>когда добавлен: ".$tlang['cldate'];
    echo "</p>";
    }}
    
    echo "<h4>Добавление языка преподавателю « ".$teacher['tname']." »</h4>";
    echo "<hr>";
?>
    <?= $this->render('_form', [
        'model' => $model,
	'langs' => $langs,
	'teacher' => $teacher,
    ]) ?>

</div>
