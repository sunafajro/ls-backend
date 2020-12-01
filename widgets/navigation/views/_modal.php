<?php
/**
 * @var View   $this
 * @var array  $data
 * @var string $id
 * @var array  $url
 */

use app\modules\school\models\File;
use yii\helpers\Html;
use yii\web\View;
?>
<div id="<?= $id ?>" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= isset($data['date']) ? date('d.m.Y - H:i', strtotime($data['date'])) : '-' ?></h4>
            </div>
            <div class="modal-body">
                <p>
                    <b>От кого:</b> <span class="text-primary"><?= $data['sender'] ?? '' ?></span>
                </p>
                <p>
                    <b>Кому:</b> <span class="text-primary"><?= $data['groupName'] ?? '' ?></span>
                </p>
                <p>
                    <b>Текст:</b>
                </p>
                <?= $data['body'] ?? '' ?>
                <?php
                    /** @var File[] $files */
                    $files = File::find()->andWhere([
                        'entity_type' => File::TYPE_ATTACHMENTS, 'entity_id' => $data['mid']
                    ])->all();
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
            </div>
            <div class="modal-footer">
                <?php
                    if ($data['canResponse']) {
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
                    echo Html::a(
                        $data['canResponse'] ? 'Прочтено' : 'Я внимательно прочитал!',
                        $url,
                        [
                            'class' => 'btn btn-primary',
                            'data' => [
                                'method' => 'post',
                            ],
                        ]
                    );
                ?>
            </div>
        </div>
    </div>
</div>