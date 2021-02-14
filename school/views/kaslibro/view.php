<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model school\models\Kaslibro */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Kaslibros', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kaslibro-view">

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
            'date',
            'operation',
            'operation_detail',
            'client',
            'executor',
            'month',
            'office',
            'code',
            'av_plus',
            'b_plus',
            'n_plus',
            'av_minus',
            'b_minus',
            'n_minus',
            'av_sum',
            'b_sum',
            'n_sum',
            'common_sum',
            'user',
            'deleted',
        ],
    ]) ?>

</div>
