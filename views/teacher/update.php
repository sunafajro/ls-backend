<?php
	use yii\helpers\Html;
	use yii\widgets\Breadcrumbs;
	$this->title = 'Система учета :: ' . Yii::t('app', 'Update teacher') . ': ' . $model->name;
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teachers'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
	foreach($statusjob as $stjob){
  		$sjob[$stjob['id']] = $stjob['name'];
	}
	$statusjobs = array_unique($sjob);
?>

<div class="row row-offcanvas row-offcanvas-left teacher-update">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
		<?= $userInfoBlock ?>
		<ul>
			<li>Адрес соц. сети указывается без префикса "http://".</li>
			<li>Дата рождения вносится в формате YYYY-MM-DD, а лучше используйте виджет календарик.</li>
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
	        'statusjobs' => $statusjobs
	    ]) ?>
	</div>
</div>
