<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Система учета :: ' . \Yii::t('app', 'Executors');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Expenses'), 'url' => ['kaslibro/index']];
$this->params['breadcrumbs'][] = \Yii::t('app', 'Executors');
?>
<div class="kaslibro-executor-index">

    <p>
        <?= Html::a(\Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <table class="table table-bordered table-stripped table-hover small">
        <thead>
            <tr>
                <th>№</th>
                <th>Наименование</th>
                <th>Дейст.</th>
            </tr>
        <tbody>
            <?php
            $i = 1;
            foreach($model as $exp) { ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $exp['name']; ?></td>
                <td><?php echo Html::a('', ['update', 'id'=>$exp['id']], ['class'=>'glyphicon glyphicon-pencil', 'title'=>\Yii::t('app','Edit')]); ?></td>
            </tr>
            <?php
            $i++;
            } ?>
        </tbody>
    </table>
</div>
