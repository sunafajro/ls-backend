<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CalcTeacher */

$this->title = Yii::t('app', 'Add teacher');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Teachers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

foreach($statusjob as $stjob){
  $sjob[$stjob['id']] = $stjob['name'];
}
$statusjobs = array_unique($sjob);

?>
<div class="row row-offcanvas row-offcanvas-left teacher-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?= $userInfoBlock ?>
		<ul>
			<li>Кнопка <b>"Добавить нового"</b> автоматически создает также пользователя в разделе Пользователи и связывает его с преподавателем.</li>
			<li>Кнопка <b>"Добавить существующего"</b> создает только карточку преподавателя. Пользователя необходимо будет создать в разделе Пользователи вручную.</li>
		</ul>
	</div>
	<div id="content" class="col-sm-6">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>

	    <?= $this->render('_form', [
	        'model' => $model,
	        'statusjobs' => $statusjobs
	    ]) ?>
	</div>
</div>
