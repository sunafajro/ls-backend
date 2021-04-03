<?php
/**
 * @var View  $this
 * @var FilterCheckbox $item
 */

use school\widgets\filters\models\FilterCheckbox;
use yii\web\View;
?>
<div class="form-group">
    <div class="checkbox">
        <label>
            <input type="checkbox" name="<?= $item->name ?? '' ?>"<?= $item->value ? 'checked' : ''; ?>>
            <?= $item->title ?? '' ?>
        </label>
    </div>
</div>
