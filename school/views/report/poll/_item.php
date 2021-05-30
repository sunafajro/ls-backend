<?php
/**
 * @var integer $questionId
 * @var array $item
 */
use yii\helpers\Html;
?>
<div class="js--question-item-block" style="border-bottom: 1px solid #aaaaaa; padding: 0.5rem 0">
    <?= Html::checkbox("PollResponse[{$questionId}][{$item['id']}][value]", false, ['disabled' => true]) ?> <?= $item['title'] ?>
    <?php if (!empty($item['textInput'])) {
        echo Html::input('text', "PollResponse[{$questionId}][{$item['id']}][text]", '', ['class' => 'form-control input-sm', 'disabled' => true]);
    } ?>
    <?php if (!empty($item['options'])) { ?>
        <div class="js--question-item-options-block" style="display: flex; flex-direction: column; margin-left:1.5rem">
            <?php foreach ($item['options'] as $option) { ?>
                <div>
                    <?= Html::checkbox("PollResponse[{$questionId}][{$item['id']}][options][{$option['id']}][value]", false, ['disabled' => true]) ?> <?= $option['title'] ?>
                    <?php if (!empty($option['textInput'])) {
                        echo Html::input('text', "PollResponse[{$questionId}][{$item['id']}][options][{$option['id']}][text]", '', ['class' => 'form-control input-sm', 'disabled' => true]);
                    } ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>
