<?php

use common\components\helpers\RequestHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View  $this
 * @var array $items
 * @var array $message
 * @var bool  $hideModal
 */
?>
<div id="navigation-panel">
    <nav id="top-nav" class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" data-toggle="collapse" data-target="#top-nav-collapse" class="navbar-toggle">
                    <span class="sr-only">Свернуть/Развернуть</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div id="top-nav-collapse" class="collapse navbar-collapse">
                <ul id="nav-links" class="navbar-nav nav">
                    <?php foreach ($items ?? [] as $item) { ?>
                        <?php
                            $options = ['title' => $item['title']];
                            if ($item['post'] ?? false) {
                                $options = RequestHelper::createLinkPostOptions($options);
                            }
                            $linkContent = Html::tag('i', '', ['class' => $item['classes'], 'aria-hidden' => 'true']);
                            if ($item['hasBadge'] ?? false) {
                                $linkContent .= Html::tag('span', $item['cnt'] ?? 0, ['class' => 'badge navigation-badge']);
                            }
                        ?>
                        <li>
                            <?= Html::a($linkContent, $item['url'], $options) ?>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>
    <?php
        if (!$hideModal && !empty($message)) {
            echo $this->render('_modal', [
                'data' => $message,
                'id'   => 'navigation-message-modal',
                'url'  => ['message/response', 'id' => $message['mid']],
            ]);
        }
    ?>
</div>