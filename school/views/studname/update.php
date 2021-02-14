<?php
/**
 * @var yii\web\View       $this
 * @var school\models\Student $model
 * @var array              $sex
 * @var string             $userInfoBlock
 * @var array              $way
 */

use yii\widgets\Breadcrumbs;

$this->title = Yii::t('app', 'Update client') . ': ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app','Update');
?>
<div class="row row-offcanvas row-offcanvas-left student-update">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
		<?= $userInfoBlock ?>
		<ul>
			<li>Имя, Фамилия и Отчество вносятся отдельно и автоматически при сохранении объединяются в полное ФИО.</li>
			<li>Содержимое поля Телефон не будет отображено, если номера вносить через меню профиля студента.</li>
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
			'sex' => $sex,
			'way' => $way,
		]) ?>
    </div>
</div>
