<?php
  use yii\helpers\Html;
  $this->title = 'Система учета :: ' . Yii::t('app', 'Messages');
  $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Messages'), 'url' => ['index']];
  $this->params['breadcrumbs'][] = $message['title'];
?>
<div class="row row-offcanvas row-offcanvas-left schedule-index">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
        <?= $userInfoBlock ?>
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
        
        <?php if ((int)$message['sended'] === 0) : ?>
          <div class="alert alert-warning">Сообщение ожидает отправки!</div>
        <?php endif; ?>
        
        <p>
            <strong>Кому:</strong>
            <span class="text-primary">
                <?= $message['receiver'] ?>
            </span>
        </p>
        
        <p>
            <strong>Текст:</strong>
            <?= $message['text'] ?>
        </p>
        
        <?php if ($message['files'] !== NULL && $message['files'] !== '0') : ?>
            <?php $link = explode('|', $message['files']) ?>
            <p>
                <strong>Файл:</strong><br />
                <?=
                  Html::img('@web/uploads/calc_message/' . $message['id'] . '/fls/' . $link[0],
                  ['width' => '200px', 'alt' => 'Image', 'class' => 'img-thumbnail'])
                ?>
            </p>
        <?php endif; ?>
        
        <?=
          (int)$message['sended'] === 0 ?
          Html::a('<i class="fa fa-envelope" aria-hidden="true"></i> Отправить',
          ['message/send', 'id' => $message['id']],
          ['class' => 'btn btn-success']) : ''
        ?> 
        
        <?php if ($message['response']) : ?>
            <?=
              Html::a('Я внимательно прочитал!',
              ['message/response', 'rid' => $message['response']],
              ['class' => 'btn btn-primary'])
            ?>
        <?php endif; ?>
    </div>
</div>
