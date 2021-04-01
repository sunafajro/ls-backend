<?php

/**
 * @var View  $this
 * @var array $item
 */

use yii\web\View;
?>
<div class="form-group">
    <label><?= $item['title'] ?? '' ?>:</label>
    <input name="<?= $item['name'] ?? '' ?>" class="form-control input-sm" value="<?= $item['value'] ?>" />
</div>