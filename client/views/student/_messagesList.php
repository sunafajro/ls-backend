<?php

/**
 * @var View $this
 * @var array $model
 */

use client\models\File;
use common\components\helpers\IconHelper;
use yii\helpers\Html;
use yii\web\View;

?>
<div class="panel panel-<?= (int)$model['type'] === 13 ? 'success' : 'info' ?>">
    <div class="panel-heading">
        <?= date('d.m.Y', strtotime($model['date'])) . ' :: ' . $model['title'] . ((int)$model['type'] === 13 ? '' : (' :: ' . $model['receiver'])) ?>
    </div>
    <div class="panel-body">
        <div class="text-justify">
            <?= $model['text'] ?>
            <?= $this->render('//layouts/_attachments', ['entityType' => File::TYPE_MESSAGE_FILES, 'entityId' => $model['id']]) ?>
        </div>
        <div class="text-right">
            <small><?= ((int)$model['type'] === 13 ? $model['sender'] : Yii::$app->user->identity->name) ?></small>
        </div>
    </div>
</div>