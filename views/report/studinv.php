<?php
use yii\helpers\Html;
$totalsum = 0;
foreach($dates as $key => $value){
    echo "<a href='#collapse-invoice-".$key."' role='button' data-toggle='collapse' aria-expanded='false' aria-controls='collapse-invoice-".$key."' class='text-warning'>".date('d.m.y', strtotime($value))." (".Yii::t('app', date('l', strtotime($value))).")</a><br />";
    echo "<div class='collapse' id='collapse-invoice-".$key."'>";
    $totaldaysum = 0;
    echo "<table class='table table-bordered table-stripped table-hover table-condensed'>";
    echo "<tbody>";
    foreach($invoices as $inv) {
        if($inv['date']==$value) {
            if($inv['visible']==0) {
                    echo "<tr class='danger'>";
            } else {
                if($inv['done']==1) { 
                    echo "<tr class='success'>";
                } else {
                    echo "<tr class='warning'>";
                }
            }
            echo "<td>#".$inv['iid'].($inv['remain']==1 ? ' (ост.)' : '')."</td>";
            echo "<td>".$inv['uname']."</td>";
            echo "<td>".Html::a($inv['sname']." → ", ['studname/view', 'id'=>$inv['sid']])." (усл. #".$inv['id'].", ".$inv['num']." зан.)</td>";
            echo "<td>".$inv['money']."</td>";
            echo "</tr>";
            if($inv['visible']==1&&$inv['remain']==0) {
                $totaldaysum = $totaldaysum + $inv['money'];
            }
            }
        }
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
    echo "<p class='text-right'>всего за день: ".$totaldaysum."</p>";
    $totalsum = $totalsum + $totaldaysum;
}
echo "<hr />";
echo "<p class='text-right'>всего по офису: ".$totalsum."</p>";
?>
