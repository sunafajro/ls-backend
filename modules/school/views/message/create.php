<?php

use app\models\Message;
use app\widgets\alert\AlertWidget;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/**
 * @var View    $this
 * @var Message $model
 * @var array   $receivers
 * @var array   $types
 * @var string  $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Messages');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Messages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add message');
?>
<div class="row row-offcanvas row-offcanvas-left message-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?>
        <div style="margin-top: 1rem">
            <h4><?= Yii::t('app', 'Hints') ?>:</h4>
            <ul>
                <li>После создания сообщения, оно не будет отправлено автоматически, это необходимо сделать вручную!</li>
			    <li>Вы можете прикрепить файл только после создания сообщения, но до его отправки!</li>
		    </ul>
        </div>
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
        
        <?= AlertWidget::widget() ?>
        
        <?= $this->render('_form', [
                'model'     => $model,
                'receivers' => $receivers,
                'types'     => $types,
        ]) ?>
    </div>
</div>
