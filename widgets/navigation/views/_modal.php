<?php
/**
 * @var yii\web\View $this
 * @var ActiveForm   $form
 * @var array        $data
 * @var string       $id
 * @var string       $type
 * @var string       $url
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
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
                    <strong>От кого:</strong> <span class="text-primary"><?= $type === 'message' ? ($data['sender'] ?? '') : ($data['user'] ?? '') ?></span>
                </p>
                    <?php if ($type === 'message') { ?>
                    <p>
                        <strong>Текст:</strong>
                    </p>
                    <?= $data['body'] ?? '' ?>
                    <?php if ($data['image'] ?? false) { ?>
                        <p><strong>Файл:</strong></p>
                        <?= Html::img($data['image'], ['alt' => 'image', 'style' => 'width: 200px']) ?>
                    <?php } ?>
                <?php } else { ?>
                    <p>Прошу подтвердить скидку <?= Html::tag('i', $data['saleName']) ?> для клиента <?= Html::a($data['clientName'], ['studname/view', 'id' => $data['clientId']]) ?>.</p>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <?php
                    $form = ActiveForm::begin([
                        'action' => [$url],
                        'method' => 'post',
                    ]);
                ?>
                <?= Html::hiddenInput('id', $type === 'message' ? ($data['rid'] ?? NULL) : ($data['sid'] ?? NULL)) ?>
                <?php if ($type === 'sale') { ?>
                    <?= Html::hiddenInput('status', 'accept', ['id' => 'navigation-modal-status-input']) ?>
                    <?= Html::submitButton('Отказать', ['class' => 'btn btn-primary', 'id' => 'navigation-modal-refuse-button']) ?>
                <?php } ?>
                <?= Html::submitButton(($type === 'message' ? 'Я внимательно прочитал!' : 'Подтвердить'), ['class' => 'btn btn-primary']) ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>