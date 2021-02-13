<?php

/**
 * @var View  $this
 * @var array $items
 */

use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\web\View;

NavBar::begin(['brandLabel' => Yii::$app->params['navBrand'], 'renderInnerContainer' => false]);
echo Nav::widget([
    'items'   => $items,
    'options' => ['class' => 'navbar-nav'],
]);
NavBar::end();