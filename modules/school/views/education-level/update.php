<?php

/**
 * @var View $this
 * @var EducationLevel $model
 */

use app\models\EducationLevel;
use app\widgets\alert\AlertWidget;
use app\widgets\userInfo\UserInfoWidget;
use yii\web\View;

$this->title = Yii::$app->params['appTitle'] . ' ' . Yii::t('app', 'Update education level');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Administration'), 'url' => ['admin/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Education levels'), 'url' => ['admin/education-levels']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="education-level-update">

    <div id="sidebar" class="col-xs-12 col-sm-12 col-md-2 col-lg-2 col-xl-2">
        <?= UserInfoWidget::widget() ?>
    </div>

    <div id="content" class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?= AlertWidget::widget() ?>
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>

</div>
