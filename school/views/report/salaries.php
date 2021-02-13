<?php
/**
 * @var View $this
 */

use school\assets\ReportSalariesAsset;
use yii\web\View;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Reports');
ReportSalariesAsset::register($this);
?>
<div id="app" data-null-year="2011" data-mode="standalone" data-url-prefix=""></div>
