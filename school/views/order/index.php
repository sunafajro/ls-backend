<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel school\models\CalcOrdersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Система учета :: '.Yii::t('app','Orders');
$this->params['breadcrumbs'][] = Yii::t('app','Orders');
?>
<div class="calc-orders-index">

    <?php
        foreach($model as $order){
            echo "<div class='panel panel-default'>";
                echo "<div class='panel panel-body'>";
                    echo "<h3>Приказ № ".$order['onumber']."</h3>";
                    echo "<h4>".$order['otitle']."</h4>";
                    echo "<p>".$order['ocontent']."</p>"; 
                echo "</div>";
            echo "</div>";
        }    
    ?>

</div>
