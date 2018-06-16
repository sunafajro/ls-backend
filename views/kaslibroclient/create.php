<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\KaslibroClient */

$this->title = 'Система учета :: ' . \Yii::t('app', 'Create');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Expenses'), 'url' => ['kaslibro/index']];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Clients'), 'url' => ['kaslibroclient/index']];
$this->params['breadcrumbs'][] = \Yii::t('app', 'Create');
?>
<div class="kaslibro-client-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
