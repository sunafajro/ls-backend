<?php

use yii\helpers\Html;

$this->title = Yii::t('app','Update client login or password');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Clients'), 'url' => ['studname/index']];
$this->params['breadcrumbs'][] = ['label' => $student->name, 'url' => ['studname/view', 'id'=>$student->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row row-offcanvas row-offcanvas-left student_login-update">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?= $userInfoBlock ?>
		<ul>
			<li>При создании уч. записи логины проверяются на уникальность.</li>
            <li>Минимальная длина пароля 8 знаков.</li>
            <li>Для разблокировки ЛК достаточно изменить пароль пользователя.</li>
		</ul>
	</div>
	<div id="content" class="col-sm-6">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
