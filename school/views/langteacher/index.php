<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel school\models\CalcLangteacherSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Calc Langteachers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calc-langteacher-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Calc Langteacher', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'calc_teacher',
            'calc_lang',
            'visible',
            'data',
            // 'user',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
