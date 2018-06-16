<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CalcService */

$this->title = 'Добавить услугу';
$this->params['breadcrumbs'][] = ['label' => 'Услуги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row row-offcanvas row-offcanvas-left service-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?= $userInfoBlock ?>
		<ul>
			<li>Заполните необходимые поля.</li>
			<li>Нажмите кнопку Добавить.</li>
		</ul>
	</div>
	<div id="content" class="col-sm-6">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
        <?= $this->render('_form', [
            'model' => $model,
            'ages' => $eduages,
            'types' => $servicetypes,
            'langs' => $languages,
            'forms' => $eduforms,
            'norms' => $timenorms,
            'city' => $cities,
            'costs' => $studnorms,
        ]) ?>
	</div>
</div>
