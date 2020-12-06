<?php

/**
 * @var View $this
 * @var User  $model
 * @var array $cities
 * @var array $offices
 * @var array $statuses
 * @var array $teachers
 */

use app\modules\school\models\User;
use app\widgets\alert\AlertWidget;
use app\widgets\userInfo\UserInfoWidget;
use yii\web\View;

$this->title = 'Система учета :: ' . Yii::t('app', 'Update user') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app','Update');
?>

<div class="row user-update">
    <div id="sidebar" class="col-xs-12 col-sm-12 col-md-2 col-lg-2 col-xl-2">
        <?= UserInfoWidget::widget() ?>
		<ul>
			<li>Поля Офис и Город разблокируются автоматически при выборе роли Менеджер Офиса</li>
		</ul>
    </div>
    <div id="content" class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?= AlertWidget::widget() ?>
        <?= $this->render('_form', [
            'model'    => $model,
            'teachers' => $teachers,
            'statuses' => $statuses,
            'offices'  => $offices,
            'cities'   => $cities,
        ]) ?>
    </div>
</div>
