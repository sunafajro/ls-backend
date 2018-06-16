<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CalcStudname */

$this->title = Yii::t('app','Add client');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calc-studname-create">


    <?= $this->render('_form', [
        'model' => $model,
        'sex' => $sex,
        'way' => $way, 
    ]) ?>

</div>
