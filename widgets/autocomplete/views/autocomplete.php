<?php

/**
 * @var array $hiddenField
 * @var array $searchField
 */

use yii\helpers\Html;
?>
<div class="form-group autocomplete-parent">
    <label class="control-label" for="js--autocomplete"><?= $searchField['label'] ?></label>
    <?= Html::input('text', '', '', ['id' => 'js--autocomplete', 'class' => 'form-control', 'autocomplete' => 'off', 'data-url' => $searchField['url']]) ?>
    <ul id="js--autocomplete-list" class="autocomplete-list"></ul>
    <?= Html::input('hidden', $hiddenField['name'], '', ['id' => 'js--autocomplete-hidden']) ?>
</div>