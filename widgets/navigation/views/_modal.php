<?php
/**
 * @var yii\web\View $this
 * @var array        $data
 * @var string       $id
 * @var array        $url
 */

use yii\helpers\Html;
?>
<div id="<?= $id ?>" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= $data['title'] ?? '' ?></h4>
            </div>
            <div class="modal-body">
                <p>
                    <strong>От кого:</strong> <span class="text-primary"><?= $data['sender'] ?? '' ?></span>
                </p>
                <p>
                    <strong>Кому:</strong> <span class="text-primary"><?= $data['groupName'] ?? '' ?></span>
                </p>
                <p>
                    <strong>Текст:</strong>
                </p>
                <?= $data['body'] ?? '' ?>
                <?php if ($data['image'] ?? false) { ?>
                    <p><strong>Файл:</strong></p>
                    <?= Html::img($data['image'], ['alt' => 'image', 'style' => 'width: 200px']) ?>
                <?php } ?>
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