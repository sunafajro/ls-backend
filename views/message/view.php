<?php
  use yii\helpers\Html;
  use yii\widgets\Breadcrumbs;
  $this->title = 'Система учета :: ' . Yii::t('app', 'Messages');
  $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Messages'), 'url' => ['index']];
  $this->params['breadcrumbs'][] = $message['title'];
?>

<div class="row row-offcanvas row-offcanvas-left message-view">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
        <?= $userInfoBlock ?>
        <?php if ((int)$message['sended'] === 0) : ?>
        <h4><?= Yii::t('app', 'Actions') ?>:</h4>
        <?=
          Html::a('<i class="fa fa-edit" aria-hidden="true"></i> ' . Yii::t('app', 'Edit'),
          ['message/update', 'id' => $message['id']],
          ['class' => 'btn btn-sm btn-warning btn-block'])
        ?>
        <?=
          Html::a('<i class="fa fa-image" aria-hidden="true"></i> ' . Yii::t('app', 'Add image'),
          ['message/upload', 'id' => $message['id']],
          ['class' => 'btn btn-sm btn-info btn-block'])
        ?>
        <?=
          Html::a('<i class="fa fa-envelope" aria-hidden="true"></i> ' . Yii::t('app', 'Send'),
          ['message/send', 'id' => $message['id']],
          ['class' => 'btn btn-sm btn-success btn-block'])
        ?>
        <?=
          Html::a('<i class="fa fa-thrash" aria-hidden="true"></i> ' . Yii::t('app', 'Delete'),
          ['message/disable', 'id' => $message['id']],
          ['class' => 'btn btn-sm btn-danger btn-block'])
        ?>
        <?php endif; ?>
        <div style="margin-top: 1rem">
            <h4><?= Yii::t('app', 'Hints') ?>:</h4>
            <ul>
                <li>Редактирование, добавление фото и удаление сообщения доступны только до его отправки!</li>
		    </ul>
        </div>
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

        <div>
            <strong>Дата:</strong> <?= date('d.m.Y', strtotime($message['date'])) ?>
        </div>
        <div>
            <strong>От кого:</strong>
            <span class="text-primary">
                <?= $message['sender'] ?>
            </span>
        </div>
        <div>
            <strong>Кому:</strong>
            <span class="text-primary">
                <?= $message['receiver'] ?>
            </span>
        </div>
        <div>
            <strong>Текст:</strong>
            <?= $message['text'] ?>
        </div>
        <?php if ($message['files'] !== NULL && $message['files'] !== '0') : ?>
            <?php $link = explode('|', $message['files']) ?>
            <div>
                <strong>Файл:</strong><br />
                <?=
                  Html::img('@web/uploads/calc_message/' . $message['id'] . '/fls/' . $link[0],
                  ['width' => '200px', 'alt' => 'Image', 'class' => 'img-thumbnail'])
                ?>
            </div>
        <?php endif; ?>
        
        <?php if ($message['response']) : ?>
            <?=
              Html::a('Я внимательно прочитал!',
              ['message/response', 'rid' => $message['response']],
              ['class' => 'btn btn-primary'])
            ?>
        <?php endif; ?>
    </div>
</div>
