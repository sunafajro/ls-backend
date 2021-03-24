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

$this->title = 'Система учета :: ' . Yii::t('app', 'Update user') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['user/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app','Update');
?>
<div class="<?= \Yii::$app->params['layout.2-column.main.class'] ?? 'row' ?> user-update">
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
