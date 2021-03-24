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
<div class="<?= \Yii::$app->params['layout.2-column.main.class'] ?? 'row' ?> user-create">
    <div class="<?= \Yii::$app->params['layout.2-column.sidebar.class'] ?? 'col-sm-2' ?>">
        <?= UserInfoWidget::widget() ?>
		<ul>
			<li>Поля Офис и Город разблокируются автоматически при выборе роли Менеджер Офиса</li>
		</ul>
    </div>
    <div class="<?= \Yii::$app->params['layout.2-column.content.class'] ?? 'col-sm-10' ?>">
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