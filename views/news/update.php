<?php

use yii\helpers\Html;

$this->title = 'Система учета :: ' . Yii::t('app','Update news');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','News'), 'url' => ['site/index']];
$this->params['breadcrumbs'][] = ['label' => $model->subject];
$this->params['breadcrumbs'][] = Yii::t('app','Update');
?>
<div class="row row-offcanvas row-offcanvas-left news-update">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?= $userInfoBlock ?>
		<ul>
			<li>Кратко опишите нововведения в системе.</li>
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
