<?php
	use yii\helpers\Html;
	use yii\widgets\Breadcrumbs;
	$this->title = 'Система учета :: ' . Yii::t('app', 'Add tax');
	$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Teachers'), 'url' => ['teacher/index']];
	$this->params['breadcrumbs'][] = ['label' => $teacher->name, 'url' => ['teacher/view', 'id'=>$teacher->id]];
	$this->params['breadcrumbs'][] = Yii::t('app', 'Add tax');
?>

<div class="row row-offcanvas row-offcanvas-left edunorm-teacher-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
		<?= $userInfoBlock ?>
		<ul>
			<li>При добавлении новой ставки, старая помечается неактивной автоматически.</li>
			<li>У преподавателя может быть только одна активная ставка по одному направлению деятельности.</li>
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
	        'norms' => $norms,
	        'tnorms' => $tnorms,
	        'teacher' => $teacher,
	        'jobPlace' => [ 1 => 'ШИЯ', 2 => 'СРР' ]
	    ]) ?>
	</div>
</div>