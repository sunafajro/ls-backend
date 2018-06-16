<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Translationlang */

$this->title = 'Система учета :: ' . Yii::t('app', 'Add language');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Languages'), 'url' => ['translate/languages']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add');
?>

<div class="row row-offcanvas row-offcanvas-left translation-language-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?= $userInfoBlock ?>
		<ul>
			<li>Укажите название языка и нажмите кнопку Добавить.</li>
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
