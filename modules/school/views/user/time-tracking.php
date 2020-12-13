<?php

/**
 * @var View                   $this
 * @var ActiveDataProvider     $dataProvider
 * @var UserTimeTrackingSearch $searchModel
 * @var User                   $user
 * @var UserTimeTrackingForm   $timeTrackingForm
 * @var array                  $can
 */

use app\components\helpers\IconHelper;
use app\modules\school\assets\UserViewAsset;
use app\modules\school\models\search\UserTimeTrackingSearch;
use app\modules\school\models\User;
use app\modules\school\models\forms\UserTimeTrackingForm;
use app\modules\school\models\UserTimeTracking;
use app\widgets\alert\AlertWidget;
use app\widgets\userInfo\UserInfoWidget;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Profile') . ": {$user->name}";
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $user->name, 'url' => ['user/view', 'id' => $user->id]];
$this->params['breadcrumbs'][] = 'Учет времени';

UserViewAsset::register($this);
?>
<div class="row user-view">
    <div id="sidebar" class="col-xs-12 col-sm-12 col-md-2 col-lg-2 col-xl-2">
        <?= UserInfoWidget::widget() ?>
        <?php if ($can['createTimeTracking']) {
            echo $this->render('time-tracking/_form', [
                    'model' => $timeTrackingForm,
            ]);
        } ?>
    </div>
    <div id="content" class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?= AlertWidget::widget() ?>
        <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel'  => $searchModel,
                'columns' => [
                    'type' => [
                        'attribute' => 'type',
                        'filter' => UserTimeTracking::getTypeLabels(),
                        'value' => function ($model) {
                            return UserTimeTracking::getTypeLabel($model->type);
                        }
                    ],
                    'start' => [
                        'attribute' => 'start',
                        'format' => ['date', 'php:d.m.Y H:i'],
                    ],
                    'end' => [
                        'attribute' => 'end',
                        'format' => ['date', 'php:d.m.Y H:i'],
                    ],
                    'comment',
                ],
        ]) ?>
    </div>
</div>
