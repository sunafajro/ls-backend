<?php

/**
 * @var yii\web\View         $this
 * @var app\models\Moneystud $model
 * @var app\models\Student   $student
 * @var array                $offices
 * @var array                $payments
 * @var string               $userInfoBlock
 */

use app\widgets\alert\AlertWidget;
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
            <li>Чекбокс "Отправить уведомление", устанавливается только при наличии адреса электронной почты в профиле клиента.</li>
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
        <?php if ((int)Yii::$app->session->get('user.ustatus') === 11) { ?>
            <table class="table table-striped table-bordered table-hover table-condensed small">
                <thead>
                    <th>№</th>
                    <th><?= Yii::t('app', 'Date') ?></th>
                    <th><?= Yii::t('app', 'Student') ?></th>
                    <th><?= Yii::t('app', 'Sum') ?></th>
                    <th><?= Yii::t('app', 'Receipt') ?></th>
                </thead>
                <tbody>
                    <?php foreach ($payments ?? [] as $payment) { ?>
                    <tr>
                        <td><?= $payment['id'] ?></td>
                        <td><?= date('d.m.Y', strtotime($payment['date'])) ?></td>
                        <td>(#<?= $payment['sid'] ?>) <?= $payment['student'] ?></td>
                        <td><?= $payment['sum'] ?> р.</td>
                        <td><?= $payment['receipt'] ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>
    </div>
</div>
