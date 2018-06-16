<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CalcBook */

$this->title = 'Create Calc Book';
$this->params['breadcrumbs'][] = ['label' => 'Calc Books', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calc-book-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
