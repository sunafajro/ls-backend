<?php

/**
 * @var View  $this
 * @var array $item
 */

use yii\web\View;
?>
<div class="form-group">
    <label><?= $item['title'] ?? '' ?>:</label>
    <select name="oid" class="form-control input-sm">
        <option value><?= $item['prompt'] ?? Yii::t('app', '-select-') ?></option>
        <?php foreach ($item['options'] as $key => $value) { ?>
            <option value="<?= $key ?>" <?= (int)$item['value'] === (int)$key ? 'selected' : ''?>>
                <?= $value ?>
            </option>
        <?php } ?>
    </select>
</div>