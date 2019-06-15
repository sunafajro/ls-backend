<?php

/**
 * @var yii\web\View            $this
 * @var yii\widgets\ActiveForm  $form
 * @var array                   $messages
 * @var array                   $messagesAll
 * @var array                   $messagesReaded
 * @var string                  $month
 * @var array                   $unreaded
 * @var string                  $userInfoBlock
 * @var string                  $year
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\Alert;
use yii\widgets\Breadcrumbs;
$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Messages');
$this->params['breadcrumbs'][] = Yii::t('app','Messages');

$js = <<< 'SCRIPT'
$(function () {
    $("[data-toggle='popover']").popover();
});
SCRIPT;
$this->registerJs($js);

for ($i = 1; $i <= 12; $i++) {
    $month_num = date('n', strtotime("$i month"));
    $month_num = $month_num < 10 ? '0' . $month_num : $month_num;
    $months[$month_num] = date('F', strtotime("$i month"));
}
ksort($months);
?>
<div class="row row-offcanvas row-offcanvas-left message-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?>
        <h4><?= Yii::t('app', 'Actions') ?></h4>
        <?= Html::a('<span class="fa fa-plus" aria-hidden="true"></span> ' . Yii::t('app', 'Add'),
          ['create'],
          ['class' => 'btn btn-success btn-sm btn-block']) ?>
        <h4><?= Yii::t('app', 'Filters') ?></h4>
        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'action' => ['message/index'],
        ]);
        ?>
        <div class="form-group">
            <select class="form-control input-sm" name="month">
                <option value="all">-все месяцы-</option>
                <?php  foreach ($months as $key => $value) { ?>
                <option <?= (int)$key === (int)$month ? 'selected' : '' ?> value="<?= $key ?>"><?= Yii::t('app', $value) ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <select class="form-control input-sm" name="year">
                <?php for ($i = 2011; $i <= date('Y'); $i++) { ?>
                <option <?= (int)$i === (int)$year ? 'selected' : '' ?> value="<?= $i ?>"><?= $i ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
	        <?= Html::submitButton('<span class="fa fa-filter" aria-hidden="true"></span> ' . Yii::t('app', 'Apply'), ['class' => 'btn btn-info btn-sm btn-block']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div id="content" class="col-sm-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
            ]); ?>
        <?php } ?>
        
        <p class="pull-left visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>
        
        <?= Alert::widget() ?>
    
        <table class="table table-stripped table-bordered table-hover table-condensed">
            <thead>
                <tr>
                    <th class="text-center"><i class="fa fa-inbox" aria-hidden="true"></i></th>
                    <th>Сообщение</th>
                    <th>Отправитель/когда</th>
                    <th>Получатель</th>
                    <th>Отчет</th>
                    <th width="10%">
                        Действия
                        <button
                          type="button"
                          class="btn btn-xs btn-default"
                          data-container="body"
                          data-toggle="popover"
                          data-placement="bottom"
                          data-content="После добавления сообщения, вы можете его отредактировать, удалить или прикрепить картинку. После отправки ни одно из этих действий доступно уже не будет."
                        >
                          ?
                        </button>
                    </th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($messages as $message) : ?>
                <?php if (!empty($unreaded) && in_array($message['id'], $unreaded)) : ?>
                <tr class="danger">
                <?php elseif ($message['sended'] !== NULL && (int)$message['sended'] === 0) : ?>
                <tr class="warning">
                <?php else : ?>
                <tr>
                <?php endif; ?>
                    <td class="text-center">
                        <i
                          class="fa fa-<?= $message['direction'] === 'out' ? 'upload' : 'download' ?>"
                          title="<?= $message['direction'] === 'out' ? Yii::t('app','Outcoming message') : Yii::t('app','Incoming message') ?>"
                          aria-hidden="true"
                        ></i>
                    </td>
                    <td>
                        <?= Html::a($message['title'], ['message/view', 'id' => $message['id']]) ?>
                    </td>
                    <td>
                        <p class="small">
                        <?php if ((int)$message['destination_id'] === 100) : ?>
                            <?= $message['sender_stn_name'] ?>
                        <?php else : ?>
                            <?= $message['sender_emp_name'] ?>
                        <?php endif; ?>
                            <br />
                            <span class="inblocktext">
                                <?= date('d.m.Y', strtotime($message['date'])) ?>
                            </span>
                        </p>
                    </td>
                    <td>
                        <p class="small">
                        <?php if ((int)$message['destination_id'] === 100 || (int)$message['destination_id'] === 5) : ?>
                            <?= $message['receiver_emp_name'] ?>
                        <?php elseif ((int)$message['destination_id'] === 13) : ?>
                            <?= $message['receiver_stn_name'] ?>
                        <?php else : ?>
                            <?= $message['destination_name'] ?>
                        <?php endif; ?>
                        </p>
                    </td>
                    <td>
                    <?php if ($message['direction'] === 'out') : ?>
                    <?php
                    $key = 0;
                    foreach ($messagesReaded as $r) {
                        if ((int)$r['id'] === (int)$message['id']) {
                            echo $r['num'] . '/';
                            $key += 1;
                        }
                    }
                    echo ($key==0) ? "0/" : "";
                    $key = 0;
                    foreach ($messagesAll as $r) {
                        if ((int)$r['id'] === (int)$message['id']) {
                            echo $r['num'];
                            $key += 1;
                        }
                    }
                    echo (int)$key === 0 ? "0" : "";
                    ?>
                    <?php endif; ?>
                    </td>
                    <td width="10%">
                        <?php if ($message['direction'] === 'out') : ?>
                        <?= (int)$message['sended'] ===0 ? Html::a('', ['message/update', 'id' => $message['id']], ['class' => 'glyphicon glyphicon-pencil', 'title' => Yii::t('app', 'Edit')]) . ' ' : '' ?>
                        <?= (int)$message['sended'] ===0 ? Html::a('', ['message/upload', 'id' => $message['id']], ['class' => 'glyphicon glyphicon-picture', 'title' => Yii::t('app', 'Add image')]) . ' ' : '' ?>
                        <?= (int)$message['sended'] ===0 ? Html::a('', ['message/send', 'id' => $message['id']], ['class' => 'glyphicon glyphicon-envelope', 'title' => Yii::t('app', 'Send')]) . ' ' : '' ?>
                        <?= (int)$message['sended'] ===0 ? Html::a('', ['message/disable', 'id' => $message['id']], ['class' => 'glyphicon glyphicon-trash', 'title' => Yii::t('app', 'Delete')]) . ' ' : '' ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
