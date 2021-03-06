<?php
/**
 * @var $this View
 */

use school\assets\ScheduleAsset;
use yii\web\View;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Schedule');
$this->params['breadcrumbs'][] = Yii::t('app', 'Schedule');
ScheduleAsset::register($this);
?>
<div id="app" data-mode="<?=Yii::$app->params['appMode']?>" data-url-prefix=""></div>