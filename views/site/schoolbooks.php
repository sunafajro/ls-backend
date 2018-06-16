<?php
use yii\helpers\Html;
?>
<?php
    // для руководителей выводим столбец действий
    if(Yii::$app->session->get('user.ustatus')==3) {
    echo Html::a('', ['schoolbook/create'], ['class' => 'btn btn-default navbar-btn glyphicon glyphicon-plus', 'title'=>Yii::t('app','Add schoolbook')]);
?>
    <table class="table table-stripped table-hover table-condensedi table-bordered">
        <thead>
            <tr>
                <th>№</th>
                <th><?= Yii::t('app','Name') ?></th>
                <th><?= Yii::t('app','Author') ?></th>
                <th><?= Yii::t('app','ISBN') ?></th>
                <th><?= Yii::t('app','Language') ?></th>
                <th><?= Yii::t('app','Actions') ?></th>
        </thead>
        <tbody>
        <tbody>
            <?php
                $i = 1;
                // распечатываем данные
                foreach($schoolbooks as $sb){ ?>
                    <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $sb['name']; ?></td>
                    <td><?php echo $sb['author']; ?></td>
                    <td><?php echo $sb['isbn']; ?></td>
                    <td><?php echo $sb['language'] ?><?td>
                    <td>
                    <?php 
                    echo Html::a('',['schoolbook/update', 'id'=>$sb['id']],['class'=>'glyphicon glyphicon-pencil']);
                    echo " ";
                    echo Html::a('',['schoolbook/delete', 'id'=>$sb['id']],['class'=>'glyphicon glyphicon-trash']);
                    ?>
                    </td>
                    </tr>
                    <?php $i++;
                }
            ?>
        </tbody>
    </table>
<?php
    }
?>

