<?php

/**
 * @var View  $this
 * @var array $student
 */

use app\modules\school\assets\InvoiceFormAsset;
use yii\web\View;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Create invoice');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Clients'), 'url' => ['studname/index']];
$this->params['breadcrumbs'][] = ['label' => $student['name'], 'url' => ['studname/view','id' => $student['id']]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create invoice');

InvoiceFormAsset::register($this);
?>
<div id="app-invoice" data-sid="<?= $student['id'] ?>" class="row">
	<div class="col-sm-12">
		<div class="alert alert-warning"><b>Подождите.</b> Загружаем форму создания счета...</div>
	</div>
</div>
