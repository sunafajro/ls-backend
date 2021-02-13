<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model school\models\CalcLangteacher */

$this->title = 'Update Calc Langteacher: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Calc Langteachers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="calc-langteacher-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
