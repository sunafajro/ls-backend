<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Status */

$this->title = 'Система учета :: ' . Yii::t('app', 'Update role');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Roles'), 'url' => ['admin/roles']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="role-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
