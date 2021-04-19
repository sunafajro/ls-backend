<?php

/**
 * @var string $entityType
 * @var integer $entityId
 */

use client\models\File;
use common\components\helpers\IconHelper;
use yii\helpers\Html;

/** @var File[] $files */
$files = File::find()->andWhere([
    'entity_type' => $entityType, 'entity_id' => $entityId
])->all();
if (!empty($files)) {
    echo Html::beginTag('div');
    $list = [];
    foreach ($files as $file) {
        $list[] = IconHelper::icon('paperclip', null, null, 'fa5') . ' ' .
            Html::tag(
                'span',
                Html::a($file->original_name, ['files/download', 'id' => $file->id], ['target' => '_blank']),
                ['class' => 'small', 'style' => 'margin-right: 5px']
            );
    }
    echo join(Html::tag('br'), $list);
    echo Html::endTag('div');
}