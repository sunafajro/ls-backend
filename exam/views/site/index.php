<?php
/**
 * @var View $this
 */

use exam\assets\SpeakingExamAsset;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = Yii::$app->name;
$this->params['breadcrumbs'][] = 'Панель управления';

SpeakingExamAsset::register($this);

$urls = json_encode([
    'exams' => Url::to(['site/get-exam-data']),
    'files' => Url::to(['site/get-exam-file']),
]);
echo Html::tag('div', '', ['id' => 'app', 'data-urls' => $urls]);
