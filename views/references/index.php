<?php

use yii\helpers\Json;

/**
 * @var yii\web\View $this
 */
$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'References');
$this->params['breadcrumbs'][] = Yii::t('app', 'References');
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
?>
<div
    id="app"
    data-mode="<?= Yii::$app->params['appMode'] ?>"
    data-urls='<?= Json::encode($urls) ?>'
></div>
<?php
    $this->registerJsFile('/js/references/vendors.js', ['position' => yii\web\View::POS_END]);
    $this->registerJsFile('/js/references/app.js', ['position' => yii\web\View::POS_END]);
?>