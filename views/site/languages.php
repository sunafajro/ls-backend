<?php
use yii\helpers\Html;
if(Yii::$app->session->get('user.ustatus')==3) {
    echo Html::a('', ['lang/create'], ['class' => 'btn btn-default navbar-btn glyphicon glyphicon-plus', 'title'=>Yii::t('app','Create language')]);
}
?>
    <table class="table table-stripped table-hover table-condensedi table-bordered">
        <thead>
            <tr>
                <th>№</th>
                <th><?= Yii::t('app','Language') ?></th>
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
                foreach($languages as $lang){
                    echo "<tr>";
                    echo "<td>".$i."</td>";
                    // имя
                    echo "<td>".$lang['name']."</td>";
                    if(Yii::$app->session->get('user.ustatus')==3) {
                        echo "<td>";
                        echo Html::a('',['lang/update', 'id'=>$lang['id']],['class'=>'glyphicon glyphicon-pencil']);
                        echo " ";
                        echo Html::a('',['lang/disable', 'id'=>$lang['id']],['class'=>'glyphicon glyphicon-trash']);
                        echo "</td>";
                    }
                    echo "</tr>";
                    $i++;
                }
            ?>
        </tbody>
    </table>