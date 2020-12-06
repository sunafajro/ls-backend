<?php

/**
 * @var View $this
 * @var User $model
 */

use app\modules\school\models\User;
use app\widgets\userInfo\UserInfoWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Profile') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->name;
?>
<div class="row user-view">
    <div id="sidebar" class="col-xs-12 col-sm-12 col-md-2 col-lg-2 col-xl-2">
        <?= UserInfoWidget::widget() ?>
    </div>
    <div id="content" class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?= DetailView::widget([
                'model'      => $model,
                'attributes' => [
                    'id',
                    [
                        'label' => Yii::t('app', 'Status'),
                        'value' => function(User $user) {
                            return $user->visible ? 'Действующий' : 'Заблокирован';
                        }
                    ],
                    'name',
                    'login',
                    'pass' => [
                        'attribute' => 'pass',
                        'format' => 'raw',
                        'value' => function(User $user) {
                            return Html::a(Yii::t('app', 'Change'), ['user/change-password', 'id' => $user->id], ['class' => 'btn btn-info']);
                        }
                    ],
                    'status' => [
                        'attribute' => 'status',
                        'value' => function(User $user) {
                            return $user->role->name;
                        }
                    ],
                    'calc_office' => [
                        'attribute' => 'calc_office',
                        'value' => function(User $user) {
                            return $user->office->name ?? '-';
                        }
                    ],
                    'calc_city' => [
                        'attribute' => 'calc_city',
                        'value' => function(User $user) {
                            return $user->city->name ?? '-';
                        }
                    ],
                    'calc_teacher' => [
                        'attribute' => 'calc_teacher',
                        'format' => 'raw',
                        'value' => function(User $user) {
                            $teacher = $user->teacher ?? null;
                            return !empty($teacher) ? Html::a($teacher->name, ['teacher/view', 'id' => $user->calc_teacher]) : '-';
                        }
                    ],
                    'logo' => [
                        'attribute' => 'logo',
                        'format' => 'raw',
                        'value' => function(User $user) {
                            $logoPath = $user->getImageWebPath();
                            return $logoPath ? Html::img($logoPath) : '-';
                        }
                    ],
                ],
        ]) ?>
    </div>
</div>
