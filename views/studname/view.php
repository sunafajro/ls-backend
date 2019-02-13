<?php
    use yii\helpers\Html;
    use yii\widgets\Breadcrumbs;
    $this->title = 'Система учета :: '.Yii::t('app', 'Students').' :: ' . $model->name;
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app','Clients'), 'url' => ['index']];
    $this->params['breadcrumbs'][] = $model->name;
    // проверяем какие данные выводить в карочку преподавателя: 1 - активные группы, 2 - завершенные группы, 3 - счета; 4 - оплаты
    if(Yii::$app->request->get('tab')){
            $tab = Yii::$app->request->get('tab');
    } else {
        // для менеджеров и руководителей по умолчанию раздел счетов
        if(Yii::$app->session->get('user.ustatus')==3||Yii::$app->session->get('user.ustatus')==4){
            $tab = 3;
        } else {
            // всем остальным раздел активных групп
            $tab = 1;
        }
    }
?>
<div class="row row-offcanvas row-offcanvas-left student-view">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
        <?= $userInfoBlock ?>
        <?php if((int)Yii::$app->session->get('user.ustatus') == 3 || (int)Yii::$app->session->get('user.ustatus') === 4): ?>
            <h4><?= Yii::t('app', 'Actions') ?>:</h4>
            <?php if($model->active==1): ?>
                <?= Html::a('<i class="fa fa-phone" aria-hidden="true"></i> ' . Yii::t('app', 'Call'), ['call/create', 'sid' => $model->id], ['class' => 'btn btn-default btn-sm btn-block']) ?>
                <?= Html::a('<i class="fa fa-times" aria-hidden="true"></i> ' . Yii::t('app', 'To inactive'), ['studname/inactive', 'id' => $model->id], ['class' => 'btn btn-warning btn-sm btn-block']) ?>
                <?= Html::a('<i class="fa fa-file" aria-hidden="true"></i> ' . Yii::t('app', 'Invoice'), ['invoice/index', 'sid' => $model->id], ['class' => 'btn btn-default btn-sm btn-block']) ?>
                <?= Html::a('<i class="fa fa-rub" aria-hidden="true"></i> ' . Yii::t('app', 'Payment'), ['moneystud/create', 'sid' => $model->id], ['class' => 'btn btn-default btn-sm btn-block']) ?>
                <?= Html::a('<i class="fa fa-list" aria-hidden="true"></i> ' . Yii::t('app', 'Detail'), ['studname/detail', 'id' => $model->id], ['class' => 'btn btn-default btn-sm btn-block']) ?>
                <?= Html::a('<i class="fa fa-gift" aria-hidden="true"></i> ' . Yii::t('app', 'Sale'), ['salestud/create', 'sid' => $model->id], ['class' => 'btn btn-default btn-sm btn-block']) ?>
                <?php if(!$clientaccess): ?>
                    <?= Html::a('<i class="fa fa-user-plus" aria-hidden="true"></i> ' . Yii::t('app', 'Account'), ['clientaccess/create', 'sid' => $model->id], ['class' => 'btn btn-default btn-sm btn-block']) ?>
		        <?php else: ?>
                    <?= Html::a('<i class="fa fa-user" aria-hidden="true"></i> ' . Yii::t('app', 'Account'), ['clientaccess/update', 'id'=>$clientaccess->id,'sid' => $model->id], ['class' => 'btn btn-default btn-sm btn-block']) ?>
		        <?php endif; ?>
                <?= Html::a('<i class="fa fa-files-o" aria-hidden="true"></i> ' . Yii::t('app', 'Contracts'), ['contract/create', 'sid' => $model->id], ['class' => 'btn btn-default btn-sm btn-block']) ?>
                <?php if ((int)Yii::$app->session->get('user.ustatus') === 3) : ?>
                    <?= Html::a('<i class="fa fa-mobile" aria-hidden="true"></i> ' . Yii::t('app', 'Phone'), ['studphone/create', 'sid' => $model->id], ['class' => 'btn btn-default btn-sm btn-block']) ?>
                <?php endif; ?>
            <?php else: ?>
                <?= Html::a('<i class="fa fa-check" aria-hidden="true"></i> ' . Yii::t('app', 'To active'), ['studname/active', 'id' => $model->id], ['class' => 'btn btn-success btn-sm btn-block']) ?>
            <?php endif; ?>
            <?= Html::a('<i class="fa fa-refresh" aria-hidden="true"></i> ' . Yii::t('app', 'Update balance'), ['studname/update-debt', 'sid' => $model->id], ['class' => 'btn btn-default btn-sm btn-block']) ?>
            <?= Html::a('<i class="fa fa-pencil" aria-hidden="true"></i> ' . Yii::t('app', 'Edit'), ['studname/update', 'id' => $model->id], ['class' => 'btn btn-default btn-sm btn-block']) ?>
            <?php if(Yii::$app->session->get('user.ustatus') == 3): ?>
                <?= Html::a('<i class="fa fa-compress" aria-hidden="true"></i> ' . Yii::t('app', 'Merge'), ['studname/merge', 'id' => $model->id], ['class' => 'btn btn-info btn-sm btn-block']) ?>
                <?= Html::a('<i class="fa fa-trash" aria-hidden="true"></i> ' . Yii::t('app', 'Delete'), 
                ['studname/delete', 'id' => $model->id], 
                [
                    'class' => 'btn btn-danger btn-sm btn-block',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure?'),
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
            <h4>Закреплен за офисом:</h4>
            <?php
                $filtered_offices = []; 
                if (isset($offices) && isset($offices['added']) && count($offices['added'])) : ?>
                <ul class="list-group" style="margin-bottom: 10px">
                <?php
                    foreach($offices['added'] as $o) : ?>
                    <li class="list-group-item list-group-item-warning">
                      <?php if ((int)$model->active === 1) : ?>
                        <?= Html::a('<i class="fa fa-trash" aria-hidden="true"></i>', ['studname/change-office', 'sid' => $model->id, 'oid' => $o['id'], 'action' => 'delete'], ['data' => ['method' => 'post']]) ?>
                      <?php endif; ?>
                      <?= $o['name'] ?>
                    </li>
                    <?php $filtered_offices[] = $o['id'] ?>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
            <?php if ((int)$model->active === 1) : ?>
              <form method="post" action="/studname/change-office?sid=<?= $model->id?>&action=add">
                <div style="margin-bottom: 10px">
                  <select class="form-control input-sm" name="office">
                    <option value="-all-">-выбрать-</option>
                    <?php if (isset($offices) && isset($offices['all']) && count($offices['all'])) : ?>
                      <?php foreach($offices['all'] as $o) : ?>
                        <?php if (!in_array($o['id'], $filtered_offices)) : ?>
                          <option value="<?= $o['id']?>"><?= $o['name'] ?></option>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </div>
                <button class="btn btn-success btn-sm btn-block" type="submit">Добавить</button>
              </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div id="content" class="col-sm-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
        ]); ?>
        <?php endif; ?>
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle</button>
		</p>
        <?php if(Yii::$app->session->hasFlash('error')): ?>
		    <div class="alert alert-danger" role="alert"><?= Yii::$app->session->getFlash('error') ?></div>
        <?php endif; ?>    
        <?php if(Yii::$app->session->hasFlash('success')): ?>
		    <div class="alert alert-success" role="alert"><?= Yii::$app->session->getFlash('success'); ?></div>
        <?php endif; ?> 
        <h3>[#<?= Html::encode($model->id) ?>] <?= Html::encode($model->name) ?> :: 
		<?= Html::encode($model->phone) ?>
        <?php if(isset($model->email) && $model->email !== '' && $model->email !== '0'): ?>
			 :: <?= Html::encode($model->email) ?>
		<?php endif; ?>
        </h3>

        <div class="row">
          <div class="<?= (($model->description || $model->address) && ($contracts && count($contracts))) ? 'col-sm-6' : 'col-sm-12' ?>">
            <?php if($model->description || $model->address): ?>
              <div class="well">
                <?= $model->description ? Html::encode($model->description) : '' ?>
                <?= $model->description !== '' && $model->address !== '' ? '<br />' : '' ?>
                <?= $model->address ? '<b>' . Yii::t('app', 'Address') . ':</b> <i>' . Html::encode($model->address) . '</i>' : '' ?>
              </div>  
		    <?php endif; ?>
          </div>
          <div class="<?= (($model->description || $model->address) && ($contracts && count($contracts))) ? 'col-sm-6' : 'col-sm-12' ?>">
            <?php if($contracts && count($contracts)): ?>
              <div class="well">
                <?php foreach($contracts as $c) : ?>
                <span style="display: block; font-style: italic">Договор № <?= Html::encode($c['number']) ?> от <?= date('d.m.y', strtotime($c['date'])) ?> оформлен на <?= Html::encode($c['signer']) ?></span>
                <?php endforeach; ?>
              </div>  
		    <?php endif; ?>
          </div>
        </div>
        <!-- блоки с информацией о скидках учтенных и оплаченных занятиях доступны только руководителям и менеджерам -->
        <?php if(Yii::$app->session->get('user.ustatus') == 3 || Yii::$app->session->get('user.ustatus') == 4): ?>
		    <?php $temporary_sales = 0; ?>
            <?php if(!empty($studsales)): ?>
                <!-- блок с информацией о временных скидках -->
                <p class="bg-warning" style="padding: 15px">
                    <button type="button" class="btn btn-xs btn-default" data-container="body" data-toggle="popover" data-placement="top" data-content="Все добавленные в этот блок ссылки будут доступны при добавлении счета."><span class="glyphicon glyphicon-info-sign"></span></button>
                    <strong><a href="#collapse-sales" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="collapse-sales" class="text-warning collapsed"><?= Yii::t('app', 'Temporary sales') ?></a></strong>
                </p>
                <div class="collapse" id="collapse-sales" aria-expanded="false" style="height: 0px">
                    <?php foreach($studsales as $ss): ?>
                    <div class="panel panel-warning">
                        <div class="panel-body">
						    <?php if ((int)$ss['visible'] === 1 && (int)$ss['approved'] !== 1): ?>
							    <span class="label label-warning">На проверке!</span>
                            <?php endif; ?>
                            <?php if((int)$ss['visible'] !== 1): ?>
                                <s>					
                            <?php else: ?>
                                <?php $temporary_sales = $temporary_sales + 1; ?>
                            <?php endif; ?>
                            <?= $ss['name'] ?>
                            <small>
                            <span class="muted">назначено когда и кем: <strong><?= date('d.m.y', strtotime($ss['date'])) ?></strong>, <strong><?= $ss['user'] ?></strong></span>
                            <?php if((int)$ss['visible'] !== 1): ?>
                                </s>
                            <?php endif; ?>
                            <?php if(isset($ss['usedby'])): ?>
                                <br /><span class="muted">когда и кем последний раз использована: <strong><?= date('d.m.y', strtotime($ss['usedate'])) ?></strong>, <strong><?= $ss['usedby'] ?></strong></span>
                            <?php endif; ?>
                            <?php if($ss['visible'] == 1 && $ss['deldate'] == '0000-00-00'): ?>
                                &nbsp;&nbsp;&nbsp;<?= ((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.ustatus') !== 4) ? '' : Html::a(Yii::t('app', 'Cancel'), ['salestud/disable', 'id'=>$ss['id']]) ?>
                            <?php endif; ?>
                            <?php if($ss['visible'] == 0): ?>
                                <br /><span class="muted">когда и кем аннулирована: <strong><?= date('d.m.y', strtotime($ss['deldate'])) ?></strong>, <strong><?= $ss['remover'] ?></strong></span>&nbsp;&nbsp;&nbsp;<?= ((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.ustatus') !== 4) ? '' : Html::a(Yii::t('app', 'Restore'), ['salestud/enable', 'id'=>$ss['id']]) ?>
                            <?php endif; ?>
                            <?php if($ss['visible'] == 1 && $ss['deldate'] != '0000-00-00'): ?>
                                <br /><span class="muted">когда и кем восстановлена: <strong><?= date('d.m.y', strtotime($ss['deldate'])) ?></strong>, <strong><?= $ss['remover'] ?></strong></span>&nbsp;&nbsp;&nbsp;<?= ((int)Yii::$app->session->get('user.ustatus') !== 3 && (int)Yii::$app->session->get('user.ustatus') !== 4) ? '' : Html::a(Yii::t('app', 'Cancel'), ['salestud/disable', 'id'=>$ss['id']]) ?>
                            <?php endif; ?>
                            </small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <!-- блок с информацией о временных скидках -->
            <?php endif; ?>
            <?php if(!empty($permsale)): ?>
                <!-- блок с информацией о постоянной скидке -->
                <p class="bg-warning text-warning" style="padding: 15px">
                    <button type="button" class="btn btn-xs btn-default" data-container="body" data-toggle="popover" data-placement="top" data-content="Данная скидка рассчитывается из общей суммы оплат студента. Применяется к счету автоматически."><span class="glyphicon glyphicon-info-sign"></span></button>
                    <strong><?= $permsale['name'] ?></strong>
                </p>
                <!-- блок с информацией о постоянной скидке -->
            <?php endif; ?>
            <!-- блок с информацией о учтенных и оплаченных занятиях -->
            <?php if(!empty($services)): ?>   
                <p class="bg-info" style="padding: 15px">
                    <button type="button" class="btn btn-xs btn-default" data-container="body" data-toggle="popover" data-placement="top" data-content="Если у студента доступных занятий меньше или равно 0, проверить занятия на которых он присутствовал будет нельзя."><span class="glyphicon glyphicon-info-sign"></span></button>
                    <strong><a href="#collapse-lessons" role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapse-lessons" class="text-info"><?= Yii::t('app', 'Payed lessons count') ?></a></strong>
                </p> 
                <div class="collapse in" id="collapse-lessons" aria-expanded="true">
                    <div class="panel panel-info">
                        <div class="panel-body">
                            <span class="muted small">
                            <?php foreach($services as $service): ?>
                            &nbsp;&nbsp;услуга <strong>#<?= $service['sid'] ?></strong> <?= $service['sname'] ?> - осталось <strong><?= $service['num'] ?></strong> занятий.
                            <?php if ($service['npd'] !== 'none') : ?>
                                <span class="label label-warning" title="Рекомендованная дата оплаты">
                                    <?= $service['npd'] ?>
                                </span>
                            <?php else : ?>
                                <span class="label label-info">Без расписания</span>
                            <?php endif; ?><br />
                            <?php endforeach; ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <!-- блок с информацией о учтенных и оплаченных занятиях -->
	    <?php endif; ?>
    <!-- блоки с информацией о скидках учтенных и оплаченных занятиях доступны только руководителям и менеджерам -->
    <?php
	// выводим блок с балансом клиента
	if($model->debt < 0) {
		// если баланс отрицательный - блок красный
		$class = 'bg-danger text-danger';
	} else {
		// если баланс положительный - блок зеленый
		$class = 'bg-success text-success';
	}
?>
        <div class="<?= $class ?>" style="padding: 15px">
            <div style="float:left">
                <button type="button" class="btn btn-xs btn-default" data-container="body" data-toggle="popover" data-placement="top" data-content="Баланс студента подсчитывается так: (сумма по оплатам - сумма по счетам) + долг по занятиям."><span class="glyphicon glyphicon-info-sign"></span></button>
                <strong><?= Yii::t('app','Balance') ?></strong></div>
            <div class='text-right'><small><span id="fullbalance" style="display: none"><?= $model->money ?> (оп) - <?= $model->invoice ?> (сч) - <?= abs($model->debt - ($model->money - $model->invoice)) ?> (зан) = </span></small> <strong><span id="balance" style="cursor: pointer"><?= $model->debt ?></span></strong> р.</div>
        </div>
    
        <ul class="nav nav-tabs user-profile-tabs" style="margin-bottom: 10px">
            <?php if(Yii::$app->session->get('user.ustatus') == 3 || Yii::$app->session->get('user.ustatus') == 4): ?>
                <li role="presentation"<?= (($tab == 3) ? ' class="active"' : '') ?>><?= Html::a(Yii::t('app','Invoices'),['studname/view','id'=>$model->id,'tab'=>3]) ?></li>
                <li role="presentation"<?= (($tab == 4) ? ' class="active"' : '') ?>><?= Html::a(Yii::t('app','Payments'),['studname/view','id'=>$model->id,'tab'=>4]) ?></li>
            <?php endif; ?>
            <li role="presentation"<?= (($tab == 1) ? ' class="active"' : '') ?>><?= Html::a(Yii::t('app','Active groups'),['studname/view','id'=>$model->id,'tab'=>1]) ?></li>
            <li role="presentation"<?= (($tab == 2) ? ' class="active"' : '') ?>><?= Html::a(Yii::t('app','Finished groups'),['studname/view','id'=>$model->id,'tab'=>2]) ?></li>
        </ul>

        <?php if ($tab == 1 || $tab == 2) {
            /* активные и завершенные группы */
            echo $this->render('_groups', [
                'groups' => $groups,
                'lessons' => $lessons,
                'schedule' => $schedule
            ]);
        } else if ($tab == 3) {
            /* счета */
            if(Yii::$app->session->get('user.ustatus') == 3 || Yii::$app->session->get('user.ustatus') == 4) {
                echo $this->render('_invoices', [
                    'invoices' => $invoices,
                    'invcount' => $invcount 
                ]);
            }
        /* выводим оплаты клиента */
        } else if ($tab == 4) {
            /* оплаты */
            if(Yii::$app->session->get('user.ustatus') == 3|| Yii::$app->session->get('user.ustatus') == 4) {
                echo $this->render('_payments', [
                    'years' => $years,
                    'payments' => $payments 
                ]);
            }
        } ?>
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