<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;
use yii\widgets\Menu;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CalcMessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Система учета :: '.Yii::t('app','Messages');
$this->params['breadcrumbs'][] = Yii::t('app','Messages');

$js = <<< 'SCRIPT'
$(function () {
    $("[data-toggle='popover']").popover();
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js);

if(Yii::$app->request->get('mon')){
    if(Yii::$app->request->get('mon')>=1&&Yii::$app->request->get('mon')<=12) {$mon = Yii::$app->request->get('mon');}
    else{$mon = NULL;}
    }
else{$mon = date('n');}

if(Yii::$app->request->get('year')){
    if(Yii::$app->request->get('year')>=2012&&Yii::$app->request->get('year')<=date('Y')) {$year = Yii::$app->request->get('year');}
    else{$year = date('Y');}
    }
else{$year = date('Y');}

?>
<div class="calc-message-index">

    <nav class="navbar navbar-default">
    <div class="container-fluid">
        <?php 
            echo Html::a("<span class='glyphicon glyphicon-plus' aria-hidden='true'></span>", ['create'], ['class' => 'btn btn-default navbar-btn']);
            ?>
        <?php 
                $form = ActiveForm::begin([
                    'method' => 'get',
                    'options' => ['class' => 'navbar-form navbar-right'],
                    'action' => ['message/index'],
                    ]);
                    ?>
            <div class="form-group">
                <select class="form-control input-sm" name="type">
                    <option value="inc">Входящие</option>
                    <option value="out" <?= (\Yii::$app->request->get('type')=='out') ? ' selected>' : '>' ?>Исходящие</option>
                </select>
                <select class="form-control input-sm" name="mon">
                    <option value="all">-все месяцы-</option>
                    <?php
                        //генерим массив с названиями месяцев
                        for($i=1;$i<=12;$i++){
                        $months[date('n',strtotime("$i month"))]=date('F',strtotime("$i month"));
                        }
                        ksort($months);
                        // распечатываем список месяцев в селект
                        foreach($months as $mkey => $month){
                        echo "<option";
                        echo ($mkey==$mon) ? ' selected ' : ' ';
                        echo "value='".$mkey."'>".\Yii::t('app',$month);
                        echo "</option>";
                        }
                    ?>
                </select>
                <select class="form-control input-sm" name="year">
                    <!-- <option value="all">-все года-</option>-->
                    <?php
                        for($i=2012;$i<=date('Y');$i++){
                        echo "<option";
                        echo ($i==$year) ? ' selected ' : ' ';
                        echo "value='".$i."'>".$i;
                        echo "</option>";
                        }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-default btn-sm">GO</button>
        <?php ActiveForm::end(); ?>
    </div><!-- /.container-fluid -->
</nav>
 
    <table class="table table-stripped table-bordered table-hover table-condensed">
        <thead>
            <tr>
                <th>Сообщение</th>
                <th>От кого/когда</th>
                <?php if(Yii::$app->request->get('type') && Yii::$app->request->get('type')=='out'): ?>
                <th>Отчет</th>
                <th width="10%">Действия 
                <button type="button" class="btn btn-xs btn-default" data-container="body" data-toggle="popover" data-placement="top" data-content="После добавления сообщения, вы можете его отредактировать, удалить или прикрепить картинку. После отправки ни одно из этих действий доступно уже не будет.">?</button></th>
                <?php endif ?>
            </tr>
        </thead>
	<?php
    foreach($messages as $message){
        // подкрашиваем строку красным если сообщение еще не прочитано
        if(!empty($messid)&&in_array($message['mid'],$messid)){
            echo "<tr class='danger'>";
        } else {
            echo "<tr>";
        }
    echo "<td><a data-toggle='modal' data-target='#modal-".$message['mid']."'>".$message['mtitle']."</a>";
    echo "<div class='modal fade modal-".$message['mid']."' id='modal-".$message['mid']."' tabindex='-1' role='dialog' aria-labelledby='modal-label-".$message['mid']."'>";
        echo "<div class='modal-dialog modal-lg' role='document'>";
            echo "<div class='modal-content'>";
                echo "<div class='modal-header'>";
                    echo "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>";
                    echo "<h4 class='modal-title' id='modal-label-".$message['mid']."'>".$message['mtitle']."</h4>";
                echo "</div>";
                echo "<div class='modal-body'>";
                    echo "<p><strong>Кому:</strong> <span class='text-primary'>";
                    // если сообщение от студента
                    if($message['mgroupid']==100||$message['mgroupid']==5){
                        echo $message['murname'];
                    }
                    elseif($message['mgroupid']==13){
                        echo $message['mstrname'];
                    }
                    else {
                        echo $message['mgroupname'];
                    }
                    echo "</span></p>";
                    echo "<p><strong>Текст:</strong> ".$message['mtext']."</p>";
                    if($message['mfile']!=NULL&&$message['mfile']!='0'){
                        $link = explode('|',$message['mfile']);
                        echo "<p><strong>Файл:</strong><br />";
                        echo Html::img('@web/uploads/calc_message/'.$message['mid'].'/fls/'.$link[0], ['width'=>'200px']);
                        echo "</p>";
                    }
                echo "</div>";
                if(!empty($messid)){
                    foreach($messid as $key => $value){
                        if($value==$message['mid']){
                        echo "<div class='modal-footer'>";
                            echo Html::a('Я внимательно прочитал!',['message/response','rid'=>$key],['class'=>'btn btn-primary']);
                        echo "</div>";
                        }
                    }
                }
            echo "</div>";
        echo "</div>";
    echo "</div>";    
    echo "</td>";
    // отправитель
    echo "<td>";
    echo "<p class='small'>";
    // если сообщение от студента
    if($message['mgroupid']==100){
	echo $message['mstsname'];
    }
    elseif($message['mgroupid']==13||$message['mgroupid']==5){
	    echo $message['musname'];
	    }
    else {
	    echo $message['musname'];
    }
    // дата отправки
    echo "<br />";
    echo "<span class='inblocktext'>";
    echo date('d.m.y H:i:s',strtotime($message['mdate']));
    echo "</span>";
    echo "</p>";
    echo "</td>";
    if(Yii::$app->request->get('type')&&Yii::$app->request->get('type')=='out'){
        // отчет по просмотрам
        echo "<td>";
        $key = 0;
        foreach($reprsp as $rsp){
	    if($rsp['mid']==$message['mid']){
	        echo $rsp['num']."/";
	        $key=+1;
	    }
        }
        echo ($key==0) ? "0/" : "";

        $key = 0;
        foreach($repall as $rall){
	    if($rall['mid']==$message['mid']){
	        echo $rall['num'];
	        $key=+1;
	        }
        }
        echo ($key==0) ? "0" : "";
        echo "</td>";
        echo "<td width='10%'>";
        echo ($message['msend']==0) ? Html::a('', ['message/update','id'=>$message['mid']], ['class'=>'glyphicon glyphicon-pencil', 'title'=>Yii::t('app', 'Edit')])." " : ' ';
        echo ($message['msend']==0) ? Html::a('', ['message/upload','id'=>$message['mid']], ['class'=>'glyphicon glyphicon-picture', 'title'=>Yii::t('app', 'Add image')])." " : " ";
        echo ($message['msend']==0) ? Html::a('', ['message/send','id'=>$message['mid']], ['class'=>'glyphicon glyphicon-envelope', 'title'=>Yii::t('app', 'Send')])." " : " ";
        echo ($message['msend']==0) ? Html::a('', ['message/disable','id'=>$message['mid']], ['class'=>'glyphicon glyphicon-trash', 'title'=>Yii::t('app', 'Delete')])." " : ' ';
        echo "</td>";
    }
    echo "</tr>";
    }
    echo "</table>";
    ?>

</div>
