<?php
/**
 * @var View $this
 */

use app\modules\exams\assets\AuditionAsset;
use yii\web\View;

$this->title = Yii::$app->params['appTitle'];
$this->params['breadcrumbs'][] = 'Панель управления';

AuditionAsset::register($this);
?>
<div id="app">

</div>
