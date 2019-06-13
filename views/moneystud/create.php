<?php

/**
 * @var yii\web\View          $this
 * @var app\models\Moneystud  $model
 * @var app\models\Student    $student
 * @var array                 $offices
 * @var string                $userInfoBlock
 */

use app\widgets\Alert;
use yii\widgets\Breadcrumbs;

$this->title = 'Система учета :: ' . Yii::t('app', 'Create payment');
if ($student) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app','Clients'), 'url' => ['studname/index']];
    $this->params['breadcrumbs'][] = ['label' => $student->name, 'url' => ['studname/view', 'id' => $student->id, 'tab' => 4]];
} else {
    $this->params['breadcrumbs'][] = Yii::t('app', 'Payments');
}
$this->params['breadcrumbs'][] = Yii::t('app', 'Create payment');
?>
<div class="row row-offcanvas row-offcanvas-left payment-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
		<?= $userInfoBlock ?>
		<ul>
			<li>Оплата помечается "остаточной", если необходимо погасить счет, по которому школа должна студенту отработать занятия, а он уже их ранее оплатил.</li>
		</ul>
	</div>
	<div id="content" class="col-sm-6">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
            ]); ?>
        <?php } ?>
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>
        <?= Alert::widget() ?>
        <?= $this->render('_form', [
			'student' => $student ?? NULL,
            'model'   => $model,
            'offices' => $offices,
        ]) ?>
    </div>
</div>