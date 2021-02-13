<?php
	use yii\helpers\Html;
	use yii\widgets\Breadcrumbs;
	$this->title = 'Система учета :: ' . Yii::t('app', 'Update client');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Clients'), 'url' => ['translate/clients']];
	$this->params['breadcrumbs'][] = $model->name;
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="row row-offcanvas row-offcanvas-left translation-client-update">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
		<?= $userInfoBlock ?>
		<ul>
			<li>Исправьте опечатки или актуализируйте информацию о клиенте и нажмите кнопку Обновить.</li>
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
