<?php
/**
 * @var integer $questionId
 * @var array $item
 * @var array $responseItem
 */
use yii\helpers\Html;
?>
<div style="border-bottom: 1px solid #aaaaaa; padding: 0.5rem 0">
    <?= Html::checkbox("PollResponse[{$questionId}][{$item['id']}][value]", $responseItem['value'] ?? 0, ['disabled' => true]) ?> <?= $item['title'] ?>
    <?php if (!empty($item['textInput'])) {
        echo Html::input('text', "PollResponse[{$questionId}][{$item['id']}][text]", $responseItem['text'] ?? '', ['class' => 'form-control input-sm', 'disabled' => true]);
    } ?>
    <?php if (!empty($item['options'])) { ?>
        <div style="display: flex; flex-direction: column; margin-left:1.5rem">
            <?php
                $responseItemOptions = $responseItem['options'] ?? [];
                foreach ($item['options'] as $option) {
                    $responseOption = $responseItemOptions[$option['id']] ?? [];
                ?>
                <div>
                    <?= Html::checkbox("PollResponse[{$questionId}][{$item['id']}][options][{$option['id']}][value]", $responseOption['value'] ?? 0, ['disabled' => true]) ?> <?= $option['title'] ?>
                    <?php if (!empty($option['textInput'])) {
                        echo Html::input('text', "PollResponse[{$questionId}][{$item['id']}][options][{$option['id']}][text]", $responseOption['text'] ?? '', ['class' => 'form-control input-sm', 'disabled' => true]);
                    } ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>
