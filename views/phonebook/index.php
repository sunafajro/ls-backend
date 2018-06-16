<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CalcPhonebookSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Телефонная книга';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calc-phonebook-index">

    <p>
        <?php
            if(Yii::$app->session->get('user.ustatus')==3) {
                echo Html::a('', ['create'], ['class' => 'btn btn-default glyphicon glyphicon-plus']);
            }
        ?>
    </p>

    <table class="table table-stripped table-hover table-condensedi table-bordered">
        <thead>
            <tr>
                <th>№</th>
                <th><?= Yii::t('app','Name') ?></th>
                <th><?= Yii::t('app','Phone') ?></th>
                <th><?= Yii::t('app','Description') ?></th>
                <?php
                    // для руководителей выводим столбец действий
                    if(Yii::$app->session->get('user.ustatus')==3) {
                        echo "<th>".Yii::t('app','Actions')."</th>";
                    }
                ?>
        </thead>
        <tbody>
            <?php
                $i = 1;
                // распечатываем данные
                foreach($phones as $phone){
                    echo "<tr>";
                    echo "<td>".$i."</td>";
                    // имя
                    echo "<td>".$phone['name']."</td>";
                    // телефон
                    echo "<td>".$phone['number']."</td>";
                    // описание
                    echo "<td>".$phone['desc']."</td>";
                    if(Yii::$app->session->get('user.ustatus')==3) {
                        echo "<td>";
                        echo Html::a('',['phonebook/update', 'id'=>$phone['id']],['class'=>'glyphicon glyphicon-pencil']);
                        echo " ";
                        echo Html::a('',['phonebook/disable', 'id'=>$phone['id']],['class'=>'glyphicon glyphicon-trash']);
                        echo "</td>";
                    }
                    echo "</tr>";
                    $i++;
                }
            ?>
        </tbody>
    </table>
</div>
