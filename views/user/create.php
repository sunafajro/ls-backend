<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'Система учета :: ' . Yii::t('app','Add user');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app','Add');
?>

<div class="row row-offcanvas row-offcanvas-left user-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= $userInfoBlock ?>
		<ul>
			<li>Поля Офис и Город разблокируются автоматически при выборе роли Менеджер Офиса</li>
		</ul>
    </div>
    <div id="content" class="col-sm-6">
        <p class="pull-left visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>
        <?= $this->render('_form', [
            'model' => $model,
            'teachers' => $teachers,
            'statuses' => $statuses,
            'offices' => $offices,
            'cities' => $cities,
        ]) ?>
    </div>
</div>