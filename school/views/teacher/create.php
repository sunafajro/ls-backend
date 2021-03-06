<?php

/**
 * @var yii\web\View $this
 * @var array        $statusjob
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('app', 'Add teacher');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Teachers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$statusjobs = ArrayHelper::map($statusjob, 'id', 'name');
?>

<div class="row row-offcanvas row-offcanvas-left teacher-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
		<?= $userInfoBlock ?>
		<ul>
			<li>Кнопка <b>"Добавить нового"</b> автоматически создает также пользователя в разделе Пользователи и связывает его с преподавателем.</li>
			<li>Кнопка <b>"Добавить существующего"</b> создает только карточку преподавателя. Пользователя необходимо будет создать в разделе Пользователи вручную.</li>
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
