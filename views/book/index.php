<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CalcBookSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \Yii::t('app','Books');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calc-book-index">

    <nav class="navbar navbar-default">
    <div class="container-fluid">
    <?= Html::a("<span class='glyphicon glyphicon-plus' aria-hidden='true'></span>", ['create'], ['class' => 'btn btn-default navbar-btn']) ?>
    </div>
    </nav>

    <!--<h1><?php /*Html::encode($this->title);*/ ?></h1>-->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

   <!-- <p>
        <?php /* Html::a('Create Calc Book', ['create'], ['class' => 'btn btn-success']); */ ?>
    </p>
-->

    <?php 

    // var_dump($sQuery);

    echo "<table class='table table-stripped table-bordered'>";
    echo "<thead>";
    echo "<tr><th>Название</th><th>ISBN</th><th>Издательство</th><th>Язык</th><th>Колич.шт.</th><th>Цена р.</th><th>Действия</th></tr>";
    echo "</thead>";
    foreach($books as $book)
    {
    echo "<tr>";
    echo "<td>".$book['bname']."</td><td>".$book['isbn']."</td><td>".$book['bpname']."</td><td>".$book['lname']."</td><td>".$book['bcount']."</td><td>".$book['bprice']."</td>";
    echo "<td><a title='Изменить' href='/index.php?r=teacher%2Fupdate&id=".$book['bid']."'><span class='glyphicon glyphicon-pencil'></span></a></td>";
    echo "</tr>";
    }
    echo "</table>";
    ?>

    <?php /* GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name:ntext',
            'author:ntext',
            'isbn:ntext',
            'description:ntext',
            // 'user',
            // 'data',
            // 'visible',
            // 'calc_bookpublisher',
            // 'calc_lang',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); */ ?>

</div>
