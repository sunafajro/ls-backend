<?php

/**
 * @var View   $this
 * @var array  $message
 */

use app\modules\school\models\MessageFile;
use app\widgets\alert\AlertWidget;
use app\widgets\userInfo\UserInfoWidget;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Messages');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Messages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $message['title'];
?>
<div class="row row-offcanvas row-offcanvas-left message-view">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= UserInfoWidget::widget() ?>
        <?php if ((int)$message['sended'] === 0) { ?>
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
            ['message/delete', 'id' => $message['id']],
            ['class' => 'btn btn-sm btn-danger btn-block', 'data-method' => 'POST', 'data-confirm' => 'Вы действительно хотите удалить это сообщение?'])
            ?>
        <?php } ?>
        <div style="margin-top: 1rem">
            <h4><?= Yii::t('app', 'Hints') ?>:</h4>
            <ul>
                <li>Редактирование, добавление фото и удаление сообщения доступны только до его отправки!</li>
		    </ul>
        </div>
    </div>
    <div id="content" class="col-sm-10">
        <div>
            <p class="visible-xs">
                <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">
                    <?= Yii::t('app', 'Toggle nav') ?>
                </button>
            </p>
        </div>

        <?= AlertWidget::widget() ?>
        
        <?php if ((int)$message['sended'] === 0) { ?>
          <div class="alert alert-warning">Сообщение ожидает отправки!</div>
        <?php } ?>

        <div>
            <b>Дата:</b> <?= date('d.m.Y', strtotime($message['date'])) ?>
        </div>
        <div>
            <b>От кого:</b>
            <span class="text-primary">
                <?= $message['sender'] ?>
            </span>
        </div>
        <div>
            <b>Кому:</b>
            <span class="text-primary">
                <?= $message['receiver'] ?>
            </span>
        </div>
        <div>
            <b>Текст:</b>
            <?= $message['text'] ?>
        </div>
        <?php
            /** @var MessageFile[] $files */
            $files = MessageFile::find()->byEntityId(intval($message['id']))->all();
            if (!empty($files)) {
                echo Html::beginTag('div');
                echo Html::tag('b', 'Файлы:');
                echo Html::beginTag('ul');
                foreach ($files as $file) {
                    echo Html::tag(
                        'li',
                        Html::a($file->original_name, ['files/download', 'id' => $file->id], ['target' => '_blank'])
                    );
                }
                echo Html::endTag('ul');
                echo Html::endTag('div');
            }
        ?>
        <?php if (!in_array($message['files'], [NULL, '', '0'])) { ?>
            <?php $link = explode('|', $message['files']) ?>
            <div>
                <b>Обложка (для объявлений):</b><br />
                <?= Html::img('@web/uploads/calc_message/' . $message['id'] . '/fls/' . $link[0],
                    ['width' => '200px', 'alt' => 'Image', 'class' => 'img-thumbnail'])
                ?>
            </div>
        <?php } ?>
        
        <?php
            echo Html::beginTag('div', ['style' => 'padding: 5px']);
            $url = ['message/response', 'id' => $message['id']];
            if ($message['canResponse']) {
                echo Html::a(
                    'Ответить',
                    $url,
                    [
                        'class' => 'btn btn-success',
                        'data' => [
                            'method' => 'post',
                            'params' => [
                                'toResponse' => true
                            ], 
                        ],
                    ]
                );
            }
            if ($message['response'] ?? false) {
                echo Html::a(
                    $message['canResponse'] ? 'Прочтено' : 'Я внимательно прочитал!',
                    $url,
                    [
                        'class' => 'btn btn-primary',
                        'data' => [
                            'method' => 'post',
                        ],
                        'style' => 'margin-left: 5px'
                    ]
                );
            }
            echo Html::endTag('div');
        ?>
    </div>
</div>
