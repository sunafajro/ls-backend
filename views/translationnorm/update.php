<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Translationnorm */

$this->title = 'Система учета :: ' . Yii::t('app', 'Update translation pay norm') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translator/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translation pay norms'), 'url' => ['translate/norms']];
$this->params['breadcrumbs'][] = $model->name;
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<div class="row row-offcanvas row-offcanvas-left translation-norm-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?= $userInfoBlock ?>
		<ul>
			<li>Вы можете исправить название или тип нормы оплаты, но не ее значение!</li>
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
