<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CalcCall */

$this->title = Yii::t('app','Create call');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Calls'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;



?>
<div class="calc-call-create">

    <?= $this->render('_form', [
        'model' => $model,
        'way' => $way,
        'servicetype' => $servicetype,
        'language' => $language,
        'level' => $level,
        'age' => $age,
        'eduform' => $eduform,
        'office' => $office,
        //'student' => $student,
		//'studdata'=>$studdata,
    ]) ?>

</div>
