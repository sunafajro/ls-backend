<?php

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View  $this
 * @var array $items
 */
$previous = $items['previous'] ?? [];
$next = $items['next'] ?? [];
?>
<nav>
    <ul class="pager">
        <li class="previous">
            <?= ($previous['show'] ?? false) ? Html::a($previous['title'], $previous['url']) : '' ?>
        </li>
        <li class="next">
            <?= ($next['show'] ?? false) ? Html::a($next['title'], $next['url']) : '' ?>
        </li>
    </ul>
</nav>