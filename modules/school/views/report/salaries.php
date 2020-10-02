<?php
/**
 * @var View $this
 */

use app\modules\school\assets\ReportSalariesAsset;
use yii\web\View;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Reports');
ReportSalariesAsset::register($this);
?>
<div id="app" data-nullyear="2011"></div>
