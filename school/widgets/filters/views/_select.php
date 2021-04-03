<?php

/**
 * @var View  $this
 * @var FilterDropDown $item
 */

use school\widgets\filters\models\FilterDropDown;
use yii\web\View;

$disablePrompt = isset($item->prompt) && $item->prompt === false;
?>
<div class="form-group">
    <label><?= $item->title ?? '' ?>:</label>
    <select name="<?= $item->name ?? '' ?>" class="form-control input-sm">
        <?php if (!$disablePrompt) { ?>
            <option value><?= $item->prompt ?? Yii::t('app', '-select-') ?></option>
        <?php } ?>
        <?php foreach ($item->options ?? [] as $key => $value) { ?>
            <option value="<?= $key ?>" <?= $item->value == $key ? 'selected' : ''?>>
                <?= $value ?>
            </option>
        <?php } ?>
    </select>
</div>