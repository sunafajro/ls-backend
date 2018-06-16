<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Edulevel */

$this->title = 'Create Edulevel';
$this->params['breadcrumbs'][] = ['label' => 'Edulevels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="edulevel-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
