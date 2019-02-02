<?php

use yii\helpers\Html;

$this->title = 'Система учета :: ' . Yii::t('app', 'Update service') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Услуги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->name;
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<div class="row row-offcanvas row-offcanvas-left service-update">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?= $userInfoBlock ?>
		<ul>
			<li>При изменнии Нормы оплаты, старая стоимость фиксируется в истории.</li>
			<li>Изменение других параметров услуги не фиксируется.</li>
		</ul>
	</div>
	<div id="content" class="col-sm-6">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
		<?= $this->render('_form', [
			'model' => $model,
			'costs' => $studnorms,
			'city'=>$cities,
		]) ?>
		<?php foreach($servicechanges as $change): ?>
			<div class="well">Дата изменения: <?= $change['date'] ?><br />
				Предыдущее значение: <?= $change['value'] ?> р.<br />
				Кем изменено: <?= $change['user'] ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
