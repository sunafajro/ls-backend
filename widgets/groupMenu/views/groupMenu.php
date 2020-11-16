<?php

/**
 * @var View  $this
 * @var array $menuItems
 */

use app\components\helpers\IconHelper;
use yii\helpers\Html;
use yii\web\View;
?>
<div class="dropdown">
    <?= Html::button(
            join(' ', [
                IconHelper::icon('list-alt'),
                Yii::t('app', 'Menu'),
                Html::tag('span', '', ['class' => 'caret'])
            ]),
            [
                'class'         => 'btn btn-default dropdown-toggle btn-sm btn-block',
                'type'          => 'button',
                'id'            => 'group-dropdown-menu',
                'data-toggle'   => 'dropdown',
                'aria-haspopup' => 'true',
                'aria-expanded' => 'true',
            ]
    ) ?>
    <ul class="dropdown-menu" aria-labelledby="group-dropdown-menu">
        <?php foreach ($menuItems as $item) {
            $itemName = ($item['icon'] ? IconHelper::icon($item['icon']) . ' ' : '') . $item['name'];
            ?>
            <li class="<?= $item['isActive'] ? 'active' : ''?>">
                <?= Html::a($itemName, $item['url'], ['class'=>'dropdown-item']) ?>
            </li>
        <?php } ?>
    </ul>
</div>
