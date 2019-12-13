<?php
/**
 * @var yii\web\View $this
 * @var ActiveForm   $form
 * @var array        $data
 * @var string       $id
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
                    $form = ActiveForm::begin([
                        'action' => [$url],
                        'method' => 'post',
                    ]);
                ?>
                <?= Html::hiddenInput('id', $data['rid'] ?? NULL) ?>
                <?= Html::submitButton('Я внимательно прочитал!', ['class' => 'btn btn-primary']) ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>