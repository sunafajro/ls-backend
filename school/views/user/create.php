<?php

/**
 * @var View $this
 * @var User  $model
 * @var array $cities
 * @var array $offices
 * @var array $statuses
 * @var array $teachers
 */

use school\models\User;
use common\widgets\alert\AlertWidget;
use school\widgets\userInfo\UserInfoWidget;
use yii\web\View;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Add user');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app','Add');
?>
<div class="row user-create">
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