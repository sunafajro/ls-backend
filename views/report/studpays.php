<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
$this->title = 'Отчет по оплатам';
$this->params['breadcrumbs'][] = $this->title;

if(Yii::$app->request->get('day')){
    if(Yii::$app->request->get('day')>=1&&Yii::$app->request->get('day')<=12) {$day = Yii::$app->request->get('day');}
    else{$day = NULL;}
    }
else{$day = date('j');}

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
<div class="site-about">
       
 <nav class="navbar navbar-default">
    <div class="container-fluid">
        <?php
                $form = ActiveForm::begin([
                    'method' => 'get',
                    'options' => ['class' => 'navbar-form navbar-right'],
                    'action' => ['report/studpays'],
                    ]);
                    ?>
        <div class="form-group">
          <select class="form-control input-sm" name="day">
                    <option value="all">-все дни-</option>
                    <?php
                        //генерим массив с названиями в
                        for($i=1;$i<=31;$i++){
                        //$months[date('n',strtotime("$i month"))]=date('F',strtotime("$i month"));
                        //}
                        //ksort($months);
                        // распечатываем список месяцев в селект
                        //foreach($months as $mkey => $month){
                        echo "<option";
                        echo ($i==$day) ? ' selected ' : ' ';
                        echo "value='".$i."'>".$i;
                        echo "</option>";
                        }
                    ?>
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

  <?php
       $office = "";
       foreach($studmoney as $money)
         {
         if($money['oname']!=$office)
           {
           if($office!="")
            { 
               echo "</div>";
             echo "</div>";
             }
           $office = $money['oname']; 
           echo "<h3>".$money['oname']."</h3>";
           echo "<div class='panel panel-success'>";
             echo "<div class='panel-heading'>".$money['mdate']."</div>";
             echo "<div class='panel-body'>";
           }
          echo "<p>".$money['sname']." -> ".$money['uname'];
          if($money['mreceipt']!=0)
             {
             echo " (".$money['mreceipt'].")";
             }
          echo " <strong>".$money['mvalue']." р.</strong></p>";
         }
  ?>
</div>
