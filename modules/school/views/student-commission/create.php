<?php

use app\models\Student;
use app\models\StudentCommission;
use app\widgets\alert\AlertWidget;
use yii\widgets\Breadcrumbs;
use yii\web\View;

/**
 * @var View              $this
 * @var StudentCommission $model
 * @var Student           $student
 * @var array             $offices
 * @var string            $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Create commission');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Clients'), 'url' => ['studname/index']];
$this->params['breadcrumbs'][] = ['label' => $student->name, 'url' => ['studname/view', 'id' => $student->id, 'tab' => 5]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create commission');
?>
<div class="row row-offcanvas row-offcanvas-left payment-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
		<?= $userInfoBlock ?>
		<ul>
            <li>Процент комиссии должен соответствовать указанному в договоре.</li>
            <li>При изменении процента сумма комиссии пересчитывается автоматически.</li>
            <li>Сумма комиссии округляется до целого чисела, но может быть задана и в виде вещественного.</li>
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
        <?= AlertWidget::widget() ?>
        <?= $this->render('_form', [
            'student' => $student ?? NULL,
            'model'   => $model,
            'offices' => $offices,
        ]) ?>
    </div>
</div>
