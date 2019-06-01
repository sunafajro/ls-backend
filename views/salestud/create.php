<?php

/**
 * @var yii\web\View        $this
 * @var app\models\Salestud $model
 * @var app\models\Student  $student
 * @var array               $sales
 * @var string              $userInfoBlock
 */

use yii\widgets\Breadcrumbs;
$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Add sale');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $student->name, 'url' => ['studname/view','id'=>$student->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add sale');
?>

<div class="row row-offcanvas row-offcanvas-left student_sale-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
		<?= $userInfoBlock ?>
		<ul>
			<li>Допускается добавление студенту нескольких скидок.</li>
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
            'sales' => $sales,
            'studentId' => $student->id,
        ]) ?>
    </div>
</div>
