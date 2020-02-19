<?php

use app\models\search\LessonSearch;
use app\models\Student;
use app\widgets\Alert;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;

/**
 * @var View               $this
 * @var ActiveDataProvider $dataProvider
 * @var ActiveForm         $form
 * @var LessonSearch       $searchModel
 * @var Student            $model
 * @var array              $commissions
 * @var array              $invoices
 * @var array              $payments
 * @var array              $groups
 * @var array              $lessons
 * @var array              $studsales
 * @var array              $services
 * @var array              $schedule
 * @var array              $years
 * @var array              $invcount
 * @var array              $permsale
 * @var string             $userInfoBlock
 * @var array              $offices
 * @var array              $contracts
 * @var array              $loginStatus
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Students') . ' :: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->name;

$roleId = (int)Yii::$app->session->get('user.ustatus');

// проверяем какие данные выводить в карочку преподавателя: 1 - активные группы, 2 - завершенные группы, 3 - счета; 4 - оплаты
if (Yii::$app->request->get('tab')) {
        $tab = Yii::$app->request->get('tab');
} else {
    // для менеджеров и руководителей по умолчанию раздел счетов
    if (in_array($roleId, [3, 4])) {
        $tab = 3;
    } else {
        // всем остальным раздел активных групп
        $tab = 1;
    }
}
?>
<div class="row row-offcanvas row-offcanvas-left student-view">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?>
        <!-- Навигация -->
        <?= $this->render('./view/_sidebar', [
                'loginStatus' => $loginStatus,
                'model'       => $model,
                'roleId'      => $roleId,
        ]) ?>
        <h4>Закреплен за офисом:</h4>
        <?php $filtered_offices = []; ?>
        <?php if (isset($offices) && isset($offices['added']) && count($offices['added'])) { ?>
            <ul class="list-group" style="margin-bottom: 10px">
            <?php foreach ($offices['added'] as $o) { ?>
                <li class="list-group-item list-group-item-warning">
                    <?php if (
                        (
                            (int)Yii::$app->session->get('user.ustatus') === 3 ||
                            (int)Yii::$app->session->get('user.ustatus') === 4
                        ) && (int)$model->active === 1
                    ) {
                        echo Html::a(
                            Html::tag('i', '', ['class' => 'fa fa-trash', 'aria-hidden' => true]),
                            ['studname/change-office', 'sid' => $model->id, 'action' => 'delete'],
                            [
                                'data' => [
                                    'method' => 'post',
                                    'params' => [
                                        'office' => $o['id'],
                                    ]
                                ],
                                'title' => Yii::t('app', 'Unbind office'),
                            ]);
                        if ($o['isMain'] !== '1') {
                            echo ' ' . Html::a(
                                Html::tag('i', '', ['class' => 'fa fa-star-o', 'aria-hidden' => true]),
                                ['studname/change-office', 'sid' => $model->id, 'action' => 'set-main'],
                                [
                                    'data' => [
                                        'method' => 'post',
                                        'params' => [
                                            'office' => $o['id'],
                                        ]
                                    ],
                                    'title' => Yii::t('app', 'Set main office'),
                                ]);
                        } else {
                            echo ' ' . Html::tag('i', '', ['class' => 'fa fa-star', 'aria-hidden' => true, 'title' => Yii::t('app', 'Main office')]);
                        }
                    } ?>
                    <?= $o['name'] ?>
                </li>
                <?php $filtered_offices[] = $o['id'] ?>
            <?php } ?>
            </ul>
        <?php } ?>
        <?php
            if (in_array($roleId, [3, 4]) && (int)$model->active === 1) { ?>
            <?php $form = ActiveForm::begin([
                'method' => 'post',
                'action' => ['studname/change-office', 'sid' => $model->id, 'action' => 'add']
            ]); ?>
            <div style="margin-bottom: 10px">
                <select class="form-control input-sm" name="office">
                <option value="-all-">-выбрать-</option>
                <?php if (isset($offices) && isset($offices['all']) && count($offices['all'])) { ?>
                    <?php foreach ($offices['all'] as $o) { ?>
                    <?php if (!in_array($o['id'], $filtered_offices)) { ?>
                        <option value="<?= $o['id']?>"><?= $o['name'] ?></option>
                    <?php } ?>
                    <?php } ?>
                <?php } ?>
                </select>
            </div>
            <?= Html::submitButton('<i class="fa fa-plus" aria-hidden="true"></i> ' . Yii::t('app', 'Add'), ['class' => 'btn btn-success btn-sm btn-block']) ?>
            <?php ActiveForm::end(); ?>
        <?php } ?>
    </div>
    <div id="content" class="col-sm-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
            ]); ?>
        <?php } ?>
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle</button>
		</p>
        <?= Alert::widget() ?>
        <?= Html::tag('h3', '[#' . $model->id . '] ' .  Html::encode($model->name)) ?>
        <?php
            $userInfo = [];
            if (isset($model->birthdate) && $model->birthdate !== '' && $model->birthdate !== '0000-00-00') {
                $userInfo[] = Html::tag('i', '', ['class' => 'fa fa-birthday-cake', 'aria-hidden' => true]) . ' ' . date('d.m.y', strtotime($model->birthdate));
            }
            if (isset($model->phone) && $model->phone !== '') {
                $userInfo[] = Html::tag('i', '', ['class' => 'fa fa-phone', 'aria-hidden' => true]) . ' ' . Html::encode($model->phone);
            }
            if (preg_match('/.+@.+/', $model->email ?? '')) {
                $userInfo[] = Html::tag('i', '', ['class' => 'fa fa-envelope', 'aria-hidden' => true]) . ' ' . Html::encode($model->email);
            }
        ?>
		<?= Html::tag('h4', implode(' :: ', $userInfo)) ?>
        <div class="row">
          <div class="<?= (($model->description || $model->address) && ($contracts && !empty($contracts))) ? 'col-sm-6' : 'col-sm-12' ?>">
            <?php if ($model->description || $model->address) { ?>
              <div class="well">
                <?= $model->description ? Html::encode($model->description) : '' ?>
                <?= $model->description !== '' && $model->address !== '' ? '<br />' : '' ?>
                <?= $model->address ? '<b>' . Yii::t('app', 'Address') . ':</b> <i>' . Html::encode($model->address) . '</i>' : '' ?>
              </div>  
		    <?php } ?>
          </div>
          <div class="<?= (($model->description || $model->address) && ($contracts && !empty($contracts))) ? 'col-sm-6' : 'col-sm-12' ?>">
            <?php if ($contracts && !empty($contracts)) { ?>
              <div class="well">
                <?php foreach($contracts as $c) : ?>
                <span style="display: block; font-style: italic">Договор № <?= Html::encode($c['number']) ?> от <?= date('d.m.y', strtotime($c['date'])) ?> оформлен на <?= Html::encode($c['signer']) ?></span>
                <?php endforeach; ?>
              </div>  
            <?php } ?>
          </div>
        </div>
        <?php if (in_array($roleId, [3, 4])) { ?>
            <?php if (!empty($studsales)) {
                echo $this->render('./view/_sales_block', [
                    'studsales' => $studsales,
                ]);
            } ?>
            <?php if (!empty($permsale)) { ?>
                <!-- блок с информацией о постоянной скидке -->
                <p class="bg-warning text-warning" style="padding: 15px">
                    <button type="button" class="btn btn-xs btn-default" data-container="body" data-toggle="popover" data-placement="top" data-content="Данная скидка рассчитывается из общей суммы оплат студента. Применяется к счету автоматически."><span class="glyphicon glyphicon-info-sign"></span></button>
                    <strong><?= $permsale['name'] ?></strong>
                </p>
                <!-- блок с информацией о постоянной скидке -->
            <?php } ?>
            <!-- блок с информацией о учтенных и оплаченных занятиях -->
            <?php if (!empty($services)) { 
                echo $this->render('./view/_services', [
                    'services'  => $services,
                    'studentId' => $model->id,
                ]);
            } ?>
            <!-- блок с информацией о учтенных и оплаченных занятиях -->
        <?php } ?>
        <!-- блоки с информацией о скидках учтенных и оплаченных занятиях доступны только руководителям и менеджерам -->
        <?php
            // выводим блок с балансом клиента
            if ($model->debt < 0) {
                // если баланс отрицательный - блок красный
                $class = 'bg-danger text-danger';
            } else {
                // если баланс положительный - блок зеленый
                $class = 'bg-success text-success';
            }
        ?>
        <div class="<?= $class ?>" style="padding: 15px">
            <div style="float:left">
                <button type="button" class="btn btn-xs btn-default" data-container="body" data-toggle="popover" data-placement="top" data-content="Баланс студента подсчитывается так: сумма по оплатам - сумма по счетам и комиссиям."><span class="glyphicon glyphicon-info-sign"></span></button>
                <strong><?= Yii::t('app','Balance') ?></strong></div>
            <div class='text-right small'>
                <span id="fullbalance" style="display: none">
                    <span data-toggle="tooltip" data-placement="top" title="Оплаты">
                        <?= $model->money ?>
                    </span> - (
                    <span data-toggle="tooltip" data-placement="top" title="Счета">
                        <?= $model->invoice ?>
                    </span> + 
                    <span data-toggle="tooltip" data-placement="top" title="Комиссии">
                        <?= round($model->commission) ?>
                    </span>) = 
                </span>
                <b>
                    <span id="balance" style="cursor: pointer" data-toggle="tooltip" data-placement="top" title="Баланс">
                        <?= $model->debt ?>
                    </span>
                </b> р.
                <?php if (in_array($roleId, [3, 4])) {
                    echo Html::a(
                            Html::tag('i', '', ['class' => 'fa fa-refresh', 'aria-hidden' => 'true']),
                            ['studname/update-debt', 'sid' => $model->id],
                            [
                                'class' => 'btn btn-default btn-xs',
                                'data' => [
                                    'method' => 'post',
                                ],
                                'title' => 'Обновить баланс',
                            ]
                        );
                } ?>
            </div>
        </div>
        <!-- Табы -->
        <ul class="nav nav-tabs user-profile-tabs" style="margin-bottom: 10px">
            <?php if (in_array($roleId, [3, 4])) { ?>
                <li role="presentation"<?= ((int)$tab === 3 ? ' class="active"' : '') ?>>
                    <?= Html::a(Yii::t('app', 'Invoices'),['studname/view','id' => $model->id, 'tab' => 3]) ?>
                </li>
                <li role="presentation"<?= ((int)$tab === 4 ? ' class="active"' : '') ?>>
                    <?= Html::a(Yii::t('app', 'Payments'),['studname/view','id' => $model->id, 'tab' => 4]) ?>
                </li>
                <li role="presentation"<?= ((int)$tab === 5 ? ' class="active"' : '') ?>>
                    <?= Html::a(Yii::t('app', 'Commissions'),['studname/view','id' => $model->id, 'tab' => 5]) ?>
                </li>
            <?php } ?>
            <li role="presentation"<?= ((int)$tab === 1 ? ' class="active"' : '') ?>>
                <?= Html::a(Yii::t('app', 'Active groups'),['studname/view','id' => $model->id, 'tab' => 1]) ?>
            </li>
            <li role="presentation"<?= ((int)$tab === 2 ? ' class="active"' : '') ?>>
                <?= Html::a(Yii::t('app', 'Finished groups'),['studname/view','id' => $model->id, 'tab' => 2]) ?>
            </li>
            <li role="presentation"<?= ((int)$tab === 6 ? ' class="active"' : '') ?>>
                <?= Html::a(Yii::t('app', 'Lessons'),['studname/view','id' => $model->id, 'tab' => 6]) ?>
            </li>
        </ul>
        <?php
            switch ($tab) {
                case 1:
                case 2:
                    /* активные и завершенные группы */
                    echo $this->render('./view/_groups', [
                        'groups' => $groups,
                        'lessons' => $lessons,
                        'schedule' => $schedule
                    ]);
                    break;
                case  3:
                    /* счета */
                    if (in_array($roleId, [3, 4])) {
                        echo $this->render('./view/_invoices', [
                            'invoices' => $invoices,
                            'invcount' => $invcount 
                        ]);
                    }
                    break;
                case 4:
                    if (in_array($roleId, [3, 4])) {
                        echo $this->render('./view/_payments', [
                            'email'    => $model->email,
                            'payments' => $payments,
                            'years'    => $years,
                        ]);
                    }
                    break;
                case 5:
                    /* оплаты */
                    if (in_array($roleId, [3, 4])) {
                        echo $this->render('./view/_commissions', [
                            'commissions' => $commissions,
                            'years'      => $years,
                        ]);
                    }
                    break;
                case 6:
                    /* занятия/комментарии */
                    echo $this->render('./view/_lessons', [
                        'dataProvider' => $dataProvider,
                        'searchModel'  => $searchModel,
                        'studentId'    => $model->id,
                    ]);
                    break;
            }
        ?>
    </div>
</div>

<?php
$balance = <<< 'SCRIPT'
$(function () { 
        $('#balance').click(
            function () {
                if($('#fullbalance').is(':visible')) {
                   $("#fullbalance").hide();
                } else {
                   $("#fullbalance").show();
                } 
            }
        );
	$('[data-toggle="popover"]').popover(); 
	$('[data-toggle="tooltip"]').tooltip();
});
SCRIPT;
$this->registerJs($balance);
?>
