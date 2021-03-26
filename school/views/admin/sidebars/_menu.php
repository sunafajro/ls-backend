<?php
/**
 * @var View $this
 * @var array $menuLinks
 */

use common\components\helpers\IconHelper;
use yii\helpers\Html;
use yii\web\View;
?>
<div class="dropdown">
    <?= Html::button(
        IconHelper::icon('book') . ' ' . Yii::t('app', 'Administration') . ' <span class="caret"></span>',
        [
            'class' => 'btn btn-default dropdown-toggle btn-sm btn-block',
            'type' => 'button',
            'id' => 'dropdownMenu',
            'data-toggle' => 'dropdown',
            'aria-haspopup' => 'true',
            'aria-expanded' => 'true',
        ]
    ) ?>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenu">
        <?php foreach($menuLinks as $link) { ?>
            <li <?= $link['active'] ? 'class="active"' : '' ?>>
                <?= Html::a($link['name'], [$link['url']], $link['classes'] ? ['class' => 'dropdown-item'] : '') ?>
            </li>
        <?php } ?>
    </ul>
</div>
