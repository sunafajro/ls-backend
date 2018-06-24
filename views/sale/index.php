<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CalcSaleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Система учета :: '.Yii::t('app','Sales');
$this->params['breadcrumbs'][] = Yii::t('app','Sales');
?>

<div id="react-root" class="row">
	<div class="col-sm-12">
		<div class="alert alert-warning"><b>Подождите.</b> Загружаем модуль скидок...</div>
	</div>
</div>

<?php
    if(!Yii::$app->user->isGuest) {
        $this->registerJsFile('/js/sales/bundle.js',  ['position' => yii\web\View::POS_END]);
    }
?>