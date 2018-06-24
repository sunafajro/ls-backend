<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\CalcCall */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Calc Calls', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calc-call-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name:ntext',
            'email:ntext',
            'description:ntext',
            'visible',
            'ok',
            'user_ok',
            'data_ok',
            'phone:ntext',
            'calc_sex',
            'calc_servicetype',
            'calc_lang',
            'calc_eduform',
            'calc_service',
            'calc_office',
            'calc_edulevel',
            'calc_eduage',
            'calc_class',
            'calc_nomination',
            'calc_way',
            'user',
            'data',
            'flag_check',
            'user_check',
            'data_check',
            'transform',
            'user_transform',
            'data_transform',
            'calc_studname',
            'user_edit',
            'data_edit',
            'user_visible',
            'data_visible',
        ],
    ]) ?>

</div>
