<?php

/**
 * @var View $this
 * @var Student $model
 * @var array $sex
 * @var array $way
 */

use common\widgets\alert\AlertWidget;
use school\models\Student;
use school\widgets\sidebarButton\SidebarButtonWidget;
use school\widgets\userInfo\UserInfoWidget;
use yii\web\View;

$this->title = Yii::t('app', 'Update client') . ': ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app','Update');
?>
<div class="row row-offcanvas row-offcanvas-left student-update">
    <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 col-xl-2 sidebar-offcanvas">
        <?= UserInfoWidget::widget() ?>
		<ul>
			<li>Имя, Фамилия и Отчество вносятся отдельно и автоматически при сохранении объединяются в полное ФИО.</li>
			<li>Содержимое поля Телефон не будет отображено, если номера вносить через меню профиля студента.</li>
		</ul>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?= AlertWidget::widget() ?>
        <?= SidebarButtonWidget::widget() ?>
		<?= $this->render('_form', [
			'model' => $model,
			'sex' => $sex,
			'way' => $way,
		]) ?>
    </div>
</div>
