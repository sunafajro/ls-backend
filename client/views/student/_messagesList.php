<?php

/**
 * @var View $this
 * @var array $model
 */

use client\models\File;
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
            <?php
                /** @var File[] $files */
                $files = File::find()->andWhere([
                    'entity_type' => File::TYPE_MESSAGE_FILES, 'entity_id' => $model['id']
                ])->all();
                if (!empty($files)) {
                    echo Html::beginTag('div');
                    $list = [];
                    foreach ($files as $file) {
                        $list[] = Html::tag('i', null, ['class' => 'fa fa-paperclip', 'aria-hidden' => 'true']) . ' ' .
                            Html::tag(
                                'span',
                                Html::a($file->original_name, ['files/download', 'id' => $file->id], ['target' => '_blank']),
                                ['class' => 'small', 'style' => 'margin-right: 5px']
                            );
                    }
                    echo join(Html::tag('br'), $list);
                    echo Html::endTag('div');
                }
            ?>
        </div>
        <div class="text-right">
            <small><?= ((int)$model['type'] === 13 ? $model['sender'] : Yii::$app->user->identity->name) ?></small>
        </div>
    </div>
</div>