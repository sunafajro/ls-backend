<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
$this->title = 'Отчет по оплатам (суммарный)';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="site-about">
    <table class="table table-stripped table-bordered table-hover table-condensed">
    <thead>
    <tr>
    <td>№</td>
    <td>Клиент</td>
    <td>Сумма</td>
    </tr>
    </thead>
    <tbody>
    <?php
       foreach($data as $money) {
           if($money['money']!=NULL) {
               echo "<tr>";
               echo "<td>".$money['sid']."</td>";
               echo "<td>".$money['sname']."</td>";
               echo "<td>".round($money['money'])."</td>";
               echo "</tr>";    
           }
       }
    ?>
    </tbody>
    </table>
</div>
