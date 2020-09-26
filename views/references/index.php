<?php

/**
 * @var View $this
 */

use app\assets\ReferencesAsset;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'References');
$this->params['breadcrumbs'][] = Yii::t('app', 'References');

ReferencesAsset::register($this);

$apiBaseUrl = '/api/references';
$urls = [
    'createItem' => "{$apiBaseUrl}/create/{name}",
    'csrf'       => '/site/csrf',
    'deleteItem' => "{$apiBaseUrl}/delete/{name}",
    'listItems'  => "{$apiBaseUrl}/list/{name}",
    'menuLinks'  => "{$apiBaseUrl}/menu-links",
    'navLinks'   => '/site/nav',
    'userInfo'   => '/user/get-info',
];

echo Html::tag('div', '', [
    'id' => 'app',
    'data-mode' => Yii::$app->params['appMode'],
    'data-urls' => Json::encode($urls)
]);
