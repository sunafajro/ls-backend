<?php

/**
 * @var View  $this
 * @var FilterTextInput $item
 */

use school\widgets\filters\models\FilterTextInput;
use yii\web\View;
?>
<div class="form-group">
    <label><?= $item->title ?? '' ?>:</label>
    <input name="<?= $item->name ?? '' ?>" class="form-control input-sm" value="<?= $item->value ?? '' ?>" />
</div>