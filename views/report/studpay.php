<?php
use yii\helpers\Html;

$total = 0;

foreach($offices as $o) {
    echo "<h4>" . $o['oname'] . "</h4>";
    $totalsum = 0;
    foreach($dates as $key => $value){
        echo "<a href='#collapse-studpays-" . $o['oid'] . "-" . $key . "' role='button' data-toggle='collapse' aria-expanded='false' aria-controls='collapse-studpays-" . $o['oid'] . "-" . $key . "' class='text-warning'>".date('d.m.y', strtotime($value))." (".Yii::t('app', date('l', strtotime($value))).")</a><br />";
        echo "<div class='collapse' id='collapse-studpays-" . $o['oid'] . "-" . $key . "'>";
        $totaldaysum = 0;
        echo "<table class='table table-bordered table-stripped table-hover table-condensed'>";
        echo "<tbody>";
        foreach($payments as $p) {
            if($p['date']==$value && $p['oid']==$o['oid']) {
                echo "<tr".($p['visible']==0 ? " class='danger'" : "").">";
                echo "<td>#".$p['mid'].($p['remain']==1 ? ' (ост.)' : '')."</td>";
                echo "<td>".Html::a($p['sname']." → ", ['studname/view', 'id'=>$p['sid']])."</td>";
                echo "<td>".$p['uname'].($p['receipt'] ? ' ('.$p['receipt'].')' : '')."</td>";
                echo "<td>".$p['money']."</td>";
                echo "</tr>";
                if($p['visible']==1&&$p['remain']==0) {
                    $totaldaysum = $totaldaysum + $p['money'];
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
    $total += $totalsum;
}
echo "<hr />";
echo "<p class='text-right'>всего: ".$total."</p>";
?>
