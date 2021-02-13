<?php

/* @var $this yii\web\View */
/* @var $model school\models\Develop */

$this->title = Yii::t('app','Update request: ') . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Bugtracker'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app','Update');
?>
<div class="develop-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
