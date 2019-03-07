<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;
use yii\widgets\Menu;

$this->title = 'Система учета :: '.Yii::t('app','Messages');
$this->params['breadcrumbs'][] = Yii::t('app','Messages');

$js = <<< 'SCRIPT'
$(function () {
    $("[data-toggle='popover']").popover();
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js);

if(Yii::$app->request->get('mon')){
    if(Yii::$app->request->get('mon')>=1&&Yii::$app->request->get('mon')<=12) {$mon = Yii::$app->request->get('mon');}
    else{$mon = NULL;}
    }
else{$mon = date('n');}

if(Yii::$app->request->get('year')){
    if(Yii::$app->request->get('year')>=2012&&Yii::$app->request->get('year')<=date('Y')) {$year = Yii::$app->request->get('year');}
    else{$year = date('Y');}
    }
else{$year = date('Y');}

?>

<div class="row row-offcanvas row-offcanvas-left message-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
        <?= $userInfoBlock ?>
        <h4><?= Yii::t('app', 'Actions') ?></h4>
        <?= Html::a('<span class="fa fa-plus" aria-hidden="true"></span> ' . Yii::t('app', 'Add'), ['create'], ['class' => 'btn btn-success btn-sm btn-block']) ?>
        <h4><?= Yii::t('app', 'Filters') ?></h4>
        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'action' => ['message/index'],
        ]);
        ?>
        <div class="form-group">
            <select class="form-control input-sm" name="mon">
                <option value="all">-все месяцы-</option>
                <?php
                    for ($i = 1; $i <= 12; $i++) {
                        $months[date('n',strtotime("$i month"))] = date('F', strtotime("$i month"));
                    }
                    ksort($months);
                ?>
                <?php  foreach ($months as $mkey => $month) : ?>
                <option <?= (int)$mkey === (int)$mon ? 'selected' : '' ?> value="<?= $mkey ?>"><?= Yii::t('app', $month) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <select class="form-control input-sm" name="year">
                <?php for ($i = 2011; $i <= date('Y'); $i++) : ?>
                <option <?= (int)$i === (int)$year ? 'selected' : '' ?> value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group">
	        <?= Html::submitButton('<span class="fa fa-filter" aria-hidden="true"></span> ' . Yii::t('app', 'Apply'), ['class' => 'btn btn-info btn-sm btn-block']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div id="content" class="col-sm-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
        ]); ?>
        <?php endif; ?>
        
        <p class="pull-left visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>
        
        <?php if(Yii::$app->session->hasFlash('error')) : ?>
        <div class="alert alert-danger" role="alert">
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
        <?php endif; ?>
   
        <?php if(Yii::$app->session->hasFlash('success')) : ?>
        <div class="alert alert-success" role="alert">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
        <?php endif; ?>
    
        <table class="table table-stripped table-bordered table-hover table-condensed">
            <thead>
                <tr>
                    <th>Сообщение</th>
                    <th>От кого/когда</th>
                    <?php if(Yii::$app->request->get('type') && Yii::$app->request->get('type')=='out'): ?>
                    <th>Отчет</th>
                    <th width="10%">Действия 
                    <button type="button" class="btn btn-xs btn-default" data-container="body" data-toggle="popover" data-placement="top" data-content="После добавления сообщения, вы можете его отредактировать, удалить или прикрепить картинку. После отправки ни одно из этих действий доступно уже не будет.">?</button></th>
                    <?php endif ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach($messages as $message): ?>
                <?php if (!empty($messid) && in_array($message['mid'], $messid)) : ?>
                <tr class="danger">
                <?php elseif ($message['msend'] !== NULL && (int)$message['msend'] === 0) : ?>
                <tr class="warning">
                <?php else : ?>
                <tr>
                <?php endif; ?>
                    <td>
                        <?= Html::a($message['mtitle'], ['message/view', 'id' => $message['mid']]) ?>
                    </td>
                    <td>
                        <p class="small">
                        <?php if ((int)$message['mgroupid'] === 100) : ?>
                            <?= $message['mstsname'] ?>
                        <?php elseif ((int)$message['mgroupid'] === 13 || (int)$message['mgroupid'] === 5) : ?>
                            <?= $message['musname'] ?>
                        <?php else : ?>
                            <?= $message['musname'] ?>
                        <?php endif; ?>
                            <br />
                            <span class="inblocktext">
                                <?= date('d.m.y H:i:s', strtotime($message['mdate'])) ?>
                            </span>
                        </p>
                    </td>
                    <?php if(Yii::$app->request->get('type') && Yii::$app->request->get('type') === 'out') :?>
                    <td>
                    <?php
                    $key = 0;
                    foreach($reprsp as $rsp) {
                        if ((int)$rsp['mid'] === (int)$message['mid']) {
                            echo $rsp['num'] . '/';
                            $key += 1;
                        }
                    }
                    echo ($key==0) ? "0/" : "";
                    $key = 0;
                    foreach ($repall as $rall) {
                        if ((int)$rall['mid'] === (int)$message['mid']) {
                            echo $rall['num'];
                            $key += 1;
                        }
                    }
                    echo (int)$key === 0 ? "0" : "";
                    ?>
                    </td>
                    <td width="10%">
                        <?= (int)$message['msend'] ===0 ? Html::a('', ['message/update', 'id' => $message['mid']], ['class' => 'glyphicon glyphicon-pencil', 'title' => Yii::t('app', 'Edit')]) . ' ' : '' ?>
                        <?= (int)$message['msend'] ===0 ? Html::a('', ['message/upload', 'id' => $message['mid']], ['class' => 'glyphicon glyphicon-picture', 'title' => Yii::t('app', 'Add image')]) . ' ' : '' ?>
                        <?= (int)$message['msend'] ===0 ? Html::a('', ['message/send', 'id' => $message['mid']], ['class' => 'glyphicon glyphicon-envelope', 'title' => Yii::t('app', 'Send')]) . ' ' : '' ?>
                        <?= (int)$message['msend'] ===0 ? Html::a('', ['message/disable', 'id' => $message['mid']], ['class' => 'glyphicon glyphicon-trash', 'title' => Yii::t('app', 'Delete')]) . ' ' : '' ?>
                    </td>
                <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
