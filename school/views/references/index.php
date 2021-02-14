<?php

/**
 * @var View $this
 */

use school\assets\ReferencesAsset;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'References');
$this->params['breadcrumbs'][] = Yii::t('app', 'References');

ReferencesAsset::register($this);

$apiBaseUrl = '/app/references';
$urls = [
    'createItem' => "{$apiBaseUrl}/create/{name}",
    'deleteItem' => "{$apiBaseUrl}/delete/{name}",
    'listItems'  => "{$apiBaseUrl}/list/{name}",
    'menuLinks'  => "{$apiBaseUrl}/menu-links",
    'csrf'       => Url::to(['site/csrf']),
    'navLinks'   => Url::to(['site/nav']),
    'userInfo'   => Url::to(['user/app-info']),
];

echo Html::tag('div', '', [
    'id' => 'app',
    'data-mode' => Yii::$app->params['appMode'],
    'data-urls' => Json::encode($urls)
]);
