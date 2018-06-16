<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Translationnorm */

$this->title = 'Система учета :: ' . Yii::t('app', 'Create translation pay norm');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translation pay norms'), 'url' => ['translate/norms']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add');
?>

<div class="row row-offcanvas row-offcanvas-left translation-norm-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?= $userInfoBlock ?>
		<ul>
			<li>Заполните данные новой нормы полаты и нажмите кнопку Добавить.</li>
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
