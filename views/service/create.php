<?php
    use yii\helpers\Html;
    use yii\widgets\Breadcrumbs;
    $this->title = 'Система учета :: ' . Yii::t('app', 'Add service');
    $this->params['breadcrumbs'][] = ['label' => 'Услуги', 'url' => ['index']];
    $this->params['breadcrumbs'][] = Yii::t('app', 'Add service');
?>

<div class="row row-offcanvas row-offcanvas-left service-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
		<?= $userInfoBlock ?>
		<ul>
			<li>Заполните необходимые поля.</li>
			<li>Нажмите кнопку Добавить.</li>
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
