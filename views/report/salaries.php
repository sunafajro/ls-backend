<?php
/**
 * @var View $this
 */

use app\assets\ReportSalariesAsset;
use yii\web\View;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Reports');
ReportSalariesAsset::register($this);
?>
<div id="app" data-nullyear="2011"></div>
