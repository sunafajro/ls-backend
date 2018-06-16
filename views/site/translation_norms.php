<?php
use yii\helpers\Html;
?>
<?php
    // для руководителей выводим столбец действий
    if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.uid')==146||Yii::$app->session->get('user.uid')==211) {
    echo Html::a('', ['translationnorm/create'], ['class' => 'btn btn-default navbar-btn glyphicon glyphicon-plus', 'title'=>Yii::t('app','Create translation norm')]);
?>
    <table class="table table-stripped table-hover table-condensedi table-bordered">
        <thead>
            <tr>
                <th>№</th>
                <th><?= Yii::t('app','Name') ?></th>
                <th><?= Yii::t('app','Type') ?></th>
                <th><?= Yii::t('app','Value') ?></th>
                <th><?= Yii::t('app','Actions') ?></th>
        </thead>
        <tbody>
            <?php
                $i = 1;
                // распечатываем данные
                foreach($norms as $norm){
                    echo "<tr>";
                    echo "<td>".$i."</td>";
                    echo "<td>".$norm['name']."</td>";
                    echo "<td>";
                    switch($norm['type']){
                    	case 1: echo Yii::t('app','Written'); break;
                    	case 2: echo Yii::t('app','Oral'); break;
                        case 3: echo Yii::t('app','Other'); break;
                    }
                    echo "</td>";
                    echo "<td>".$norm['value']."</td>";
                    echo "<td>";
                    echo Html::a('',['translationnorm/update', 'id'=>$norm['id']],['class'=>'glyphicon glyphicon-pencil']);
                    echo " ";
                    echo Html::a('',['translationnorm/disable', 'id'=>$norm['id']],['class'=>'glyphicon glyphicon-trash']);
                    echo "</td>";
                    echo "</tr>";
                    $i++;
                }
            ?>
        </tbody>
    </table>
<?php
    }
?>
