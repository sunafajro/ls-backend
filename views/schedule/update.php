<?php

use yii\helpers\Html;

$this->title = 'Система учета :: ' . Yii::t('app','Update Lesson');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Schedule'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app','Update');
?>

<div class="row row-offcanvas row-offcanvas-left schedule-update">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?= $userInfoBlock ?>
		<ul>
			<li>Список "Группа" динамический и разблокируется только после выбора преподавателя.</li>
			<li>Список "Кабинет" динамический и разблокируется только после выбора офиса.</li>
		</ul>
    </div>
	<div id="content" class="col-sm-6">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
		<?= $this->render('_form', [
			'model' => $model,
			'teachers'=>$teachers,
			'offices'=>$offices,
			'groups'=>$groups,
			'cabinets'=>$cabinets,
			'days' => $days,
		]) ?>
	</div>
</div>
