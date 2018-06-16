<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Bugtracker');
$this->params['breadcrumbs'][] = $this->title;

$types = ['1'=>Yii::t('app','New features'), '2'=>Yii::t('app','Bug fixes'), '3'=>Yii::t('app','Minor changes')];
$levels = ['1'=>Yii::t('app','High'), '2'=>Yii::t('app','Medium'), '3'=>Yii::t('app','Low')];
$statuses = ['1'=>Yii::t('app','Published'), '2'=>Yii::t('app','Closed')];
?>
<div class="develop-index">

    <p>
        <?= Html::a(Yii::t('app','Create request'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php
        $i=1;
    ?>
    <table class='table table-bordered table-hover table-stripped table-condensed'>
        <thead>
            <tr>
                <th>#</th>
                <th><?= Yii::t('app', 'Type') ?></th>
                <th><?= Yii::t('app', 'Published') ?></th>
                <th><?= Yii::t('app', 'Description') ?></th>
                <th><?= Yii::t('app', 'Severity') ?></th>
                <!--<th><?= Yii::t('app', 'Status') ?></th>-->
                <th><?= Yii::t('app', 'Closed') ?></th>
                <?php
                    if(Yii::$app->session->get('user.uid')=='139') { 
                        echo "<th>".Yii::t('app','Actions')."</th>"; 
                    }
                ?>
            </tr>
        </thead>
        <tbody>
        <?php
            foreach($model as $m) {
        ?>
            <?php
                switch($m['status']) {
                    case 1: echo "<tr class='warning'>"; break;
                    case 2: echo "<tr class='success'>"; break;
                }
            ?>
                <td><?= $i?></td>
                <td><?= $types[$m['type']] ?></td>
                <td><?php 
                        echo date('d-m-y', strtotime($m['creation_date']));
                        echo "<br>";
                        echo "<em>".$m['creation_user']."</em>";
                    ?></td>
                <td><?= $m['description'] ?></td>
                <td><?php 
                    switch($m['severity']) {
                        case 1: echo "<span class='label label-danger'>".$levels[$m['severity']]."</span>"; break;
                        case 2: echo "<span class='label label-warning'>".$levels[$m['severity']]."</span>"; break;
                        case 3: echo "<span class='label label-info'>".$levels[$m['severity']]."</span>"; break;
                    }
                 ?>
                </td>
                <!--<td><?= $statuses[$m['status']] ?></td>-->
		<td><?= ($m['status'] == 2) ? date('d-m-y', strtotime($m['close_date']))."<br><em>".$m['close_user']."</em>" : '' ?></td>
                <?php
                    if(Yii::$app->session->get('user.uid')=='139') {
                        echo "<td>";
                        if($m['status'] == 1) {
				echo Html::a('',['develop/update','id'=>$m['id']], ['class'=>'glyphicon glyphicon-pencil']);
				echo " ";
                            echo Html::a('',['develop/close','id'=>$m['id']], ['class'=>'glyphicon glyphicon-lock']);
                        }
                        echo "</td>"; 
                    }
                ?>

            </tr>
        <?php 
            $i++;
            } 
        ?>
        <tbody>
    </table>
</div>
