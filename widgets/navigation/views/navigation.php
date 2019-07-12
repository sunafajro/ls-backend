<?php

/**
 * @var yii\web\View $this
 * @var array        $items
 */

use yii\helpers\Html;
?>
<div id="navigation-panel">
    <nav id="top-nav" class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" data-toggle="collapse" data-target="#top-nav-collapse" class="navbar-toggle">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div id="top-nav-collapse" class="collapse navbar-collapse">
                <ul id="nav-links" class="navbar-nav nav">
                    <li style="padding: 13px 10px">
                        <span id="navigation-timer" class="label label-default" title="Время до автоматического выхода из системы">14:53</span>
                    </li>
                    <?php foreach ($items ?? [] as $item) { ?>
                        <?php
                            $options = ['title' => $item['title']];
                            if ($item['post'] ?? false) {
                                $options['data-method'] = 'post';
                            }
                            $linkContent = Html::tag('i', '', ['class' => $item['classes'], 'aria-hidden' => 'true']);
                            if ($item['hasBadge'] ?? false) {
                                $linkContent .= Html::tag('span', $item['cnt'] ?? 0, ['class' => 'badge navigation-badge']);
                            }
                        ?>
                        <li>
                            <?= Html::a(
                                $linkContent,
                                $item['url'],
                                $options
                            ) ?>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>
</div>