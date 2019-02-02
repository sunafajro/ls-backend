<?php

use yii\helpers\Html;

$this->title = Yii::t('app','Create call');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Calls'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row row-offcanvas row-offcanvas-left call-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<div id="main-menu"></div>
        <?= $userInfoBlock ?>
		<ul>
			<li>Часть полей появляется только при выборе определенных вариантов.</li>
            <li>Используйте поле "Привязать к клиенту", чтобы связать звонок с существующим клиентом.</li>
		</ul>
	</div>
	<div id="content" class="col-sm-6">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
        <?= $this->render('_form', [
            'model' => $model,
            'way' => $way,
            'servicetype' => $servicetype,
            'language' => $language,
            'level' => $level,
            'age' => $age,
            'eduform' => $eduform,
            'office' => $office,
        ]) ?>
	</div>
</div>