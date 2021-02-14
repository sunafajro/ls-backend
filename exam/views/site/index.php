<?php
/**
 * @var View $this
 */

use exam\assets\AuditionAsset;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = Yii::$app->params['appTitle'];
$this->params['breadcrumbs'][] = 'Панель управления';

AuditionAsset::register($this);

$urls = json_encode([
    'exams' => Url::to(['site/get-exam-data']),
    'files' => Url::to(['site/get-exam-file']),
]);
echo Html::tag('div', '', ['id' => 'app', 'data-urls' => $urls]);
