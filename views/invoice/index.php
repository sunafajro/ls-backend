<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CalcInvoicestud */

$this->title = 'Система учета :: ' . Yii::t('app', 'Create invoice');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Clients'), 'url' => ['studname/index']];
$this->params['breadcrumbs'][] = ['label' => $student['name'], 'url' => ['studname/view','id'=>$student['id']]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create invoice');
?>

<div id="react-invoices-root" class="row">
	<div class="col-sm-12">
		<div class="alert alert-warning"><b>Подождите.</b> Загружаем форму создания счета...</div>
	</div>
</div>

<?php
    if(!Yii::$app->user->isGuest) {
        $this->registerJsFile('/js/invoices/bundle.js',  ['position' => yii\web\View::POS_END]);
    }
?>
