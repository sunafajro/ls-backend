<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model school\models\KaslibroOperation */

$this->title = 'Система учета :: ' . \Yii::t('app', 'Create');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Expenses'), 'url' => ['kaslibro/index']];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Operations'), 'url' => ['kaslibrooperation/index']];
$this->params['breadcrumbs'][] = \Yii::t('app', 'Create');
?>
<div class="kaslibro-operation-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
