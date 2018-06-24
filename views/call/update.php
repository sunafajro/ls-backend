<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CalcCall */

$this->title = Yii::t('app','Update call').': ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Calls'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app','Update');

?>
<div class="calc-call-update">

    <?= $this->render('_form', [
        'model' => $model,
        'way' => $way,
        'servicetype' => $servicetype,
        'language' => $language,
        'level' => $level,
        'age' => $age,
        'eduform' => $eduform,
        'office' => $office,
        'student' => $student,
        'service' => $service,
    ]) ?>

</div>
