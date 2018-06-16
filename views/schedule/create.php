<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CalcSchedule */

$this->title = 'Система учета :: ' . Yii::t('app','Add lesson');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Schedule'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app','Add lesson');

?>
<div class="row row-offcanvas row-offcanvas-left schedule-create">
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
			'teachers' => $teachers,
			'offices' => $offices,
			'days' => $days,
		]) ?>
	</div>
</div>
