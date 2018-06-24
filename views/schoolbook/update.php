<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Schoolbook */

$this->title = Yii::t('app', 'Update book') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'References'), 'url' => ['site/reference']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Books'), 'url' => ['site/reference', 'type'=>12]];
$this->params['breadcrumbs'][] = ['label' => $model->name];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

?>
<div class="schoolbook-update">

    <?= $this->render('_form', [
        'model' => $model,
        'languages' => $languages,
    ]) ?>

</div>
