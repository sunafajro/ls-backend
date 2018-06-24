<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Translation */

$this->title = Yii::t('app', 'Create translation');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row row-offcanvas row-offcanvas-left translation-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= $userInfoBlock ?>
		<ul>
			<li>Положительное значение в поле Корректирровка суммы добавляется к стоимости счета.</li>
			<li>Отрицательное значение в поле Корректирровка суммы вычитается к стоимости счета.</li>
		</ul>
	</div>
	<div id="content" class="col-sm-6">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
        <?= $this->render('_form', [
            'model' => $model,
            'language' => $language,
            'client' => $client,
            'translator' => $translator,
            'norm' => $norm,
        ]) ?>
    </div>
</div>
