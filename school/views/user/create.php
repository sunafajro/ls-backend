<?php

/**
 * @var View $this
 * @var User  $model
 * @var array $cities
 * @var array $offices
 * @var array $roles
 * @var array $teachers
 */

use school\models\User;
use common\widgets\alert\AlertWidget;
use school\widgets\sidebarButton\SidebarButtonWidget;
use school\widgets\userInfo\UserInfoWidget;
use yii\web\View;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Add user');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app','Add');
$teachers = array_merge([0 => Yii::t('app', 'Create new teacher')], $teachers);
?>
<div class="row row-offcanvas row-offcanvas-left user-create">
    <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 col-xl-2 sidebar-offcanvas">
        <?= UserInfoWidget::widget() ?>
		<ul>
			<li>Поля Офис и Город разблокируются автоматически при выборе роли Менеджер Офиса</li>
		</ul>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?= AlertWidget::widget() ?>
        <?= SidebarButtonWidget::widget() ?>
        <?= $this->render('_form', [
                'model' => $model,
                'teachers' => $teachers,
                'roles' => $roles,
                'offices' => $offices,
                'cities' => $cities,
        ]) ?>
    </div>
</div>