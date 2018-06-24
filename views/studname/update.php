<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CalcStudname */

$this->title = Yii::t('app', 'Update client') . ': ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app','Update');
?>
<div class="row row-offcanvas row-offcanvas-left student-update">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?= $userInfoBlock ?>
		<ul>
			<li>Имя, Фамилия и Отчество вносятся отдельно и автоматически при сохранении объединяются в полное ФИО.</li>
			<li>Содержимое поля Телефон не будет отображено, если номера вносить через меню профиля студента.</li>
		</ul>
	</div>
	<div id="content" class="col-sm-6">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
    <?= $this->render('_form', [
        'model' => $model,
        'sex' => $sex,
        'way' => $way,
    ]) ?>
    </div>
</div>
