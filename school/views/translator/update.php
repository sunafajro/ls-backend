<?php
	use yii\helpers\Html;
	use yii\widgets\Breadcrumbs;
	$this->title = 'Система учета :: ' . Yii::t('app', 'Update translator');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translators'), 'url' => ['translate/translators']];
	$this->params['breadcrumbs'][] = $model->name;
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<div class="row row-offcanvas row-offcanvas-left translator-update">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
        <?= $userInfoBlock ?>
		<ul>
			<li>Укажите данные нового переводчика.</li>
			<li>В поле Сайт, можно указать адрес профиля социальной сети или другой ресурс.</li>
		</ul>
	</div>
	<div id="content" class="col-sm-6">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
		<?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
        ]); ?>
        <?php endif; ?>
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
	    <?= $this->render('_form', [
	        'model' => $model,
	    ]) ?>
    </div>
</div>