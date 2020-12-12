<?php

/**
 * @var View                 $this
 * @var User                 $user
 * @var UserTimeTrackingForm $model
 * @var array                $can
 */

use app\components\helpers\IconHelper;
use app\modules\school\assets\UserViewAsset;
use app\modules\school\models\User;
use app\modules\school\models\forms\UserTimeTrackingForm;
use app\widgets\alert\AlertWidget;
use app\widgets\userInfo\UserInfoWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\DetailView;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Profile') . ": {$user->name}";
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $user->name;

UserViewAsset::register($this);
?>
<div class="row user-view">
    <div id="sidebar" class="col-xs-12 col-sm-12 col-md-2 col-lg-2 col-xl-2">
        <?= UserInfoWidget::widget() ?>
        <?php if ($can['createTimeTracking']) {
            echo $this->render('time-tracking/_form', [
                    'model' => $model,
            ]);
        } ?>
    </div>
    <div id="content" class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?= AlertWidget::widget() ?>
            <?php if ($can['updateUser']) { ?>
            <div style="margin-bottom: 1rem">
                <?= Html::a(
                    IconHelper::icon('edit') . ' ' . Yii::t('app', 'Update'),
                    ['user/update', 'id' => $user->id],
                    ['class' => 'btn btn-info']
                ) ?>
                <?= Html::a(
                    IconHelper::icon($user->visible ? 'times' : 'check') . ' ' . Yii::t('app', $user->visible ? 'Disable' : 'Enable'),
                    ['user/' . ($user->visible ? 'disable' : 'enable'), 'id' => $user->id],
                    ['class' => 'btn btn-' . ($user->visible ? 'danger' : 'success')]
                ) ?>
            </div>
        <?php } ?>
        <?= DetailView::widget([
                'model'      => $user,
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
                        'value' => function(User $user) use ($can) {
                            $html = [];
                            if ($can['updatePassword']) {
                                $html[] = Html::button(
                                    IconHelper::icon('edit') . ' ' . Yii::t('app', 'Change'),
                                    ['class' => 'btn btn-info btn-xs js--change-user-password']
                                );
                                $html[] = Html::beginTag('div', ['class' => 'form-group', 'style' => 'margin-bottom: 0']);
                                $html[] = Html::beginTag('div', ['class' => 'input-group', 'style' => 'display: none']);
                                $html[] = Html::input('hidden', Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken());
                                $html[] = Html::input('password', Html::getInputName($user, 'pass'), '', ['class' => 'form-control input-sm']);
                                $html[] = Html::tag(
                                    'span',
                                    Html::button(
                                        IconHelper::icon('save'),
                                        [
                                            'class'    => 'btn btn-default btn-sm js--save-user-password',
                                            'data-url' => Url::to(['user/change-password', 'id' => $user->id]),
                                        ]
                                    ),
                                    ['class' => 'input-group-btn']
                                );
                                $html[] = Html::endTag('div');
                                $html[] = Html::tag('div', '', ['class' => 'help-block', 'style' => 'display:none']);
                                $html[] = Html::endTag('div');
                            } else {
                                $html[] = '-';
                            }

                            return join('', $html);
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
                        'value' => function(User $user) use ($can) {
                            $logoPath = $user->getImageWebPath();
                            $html = [];
                            if ($can['updateUser']) {
                                $html[] = Html::tag(
                                    'div',
                                    Html::a(
                                        IconHelper::icon('picture-o') . ' ' . Yii::t('app', 'Change'),
                                        ['user/upload', 'id' => $user->id],
                                        ['class' => 'btn btn-info btn-xs']
                                    ),
                                    ['style' => 'margin-bottom: 1rem']
                                );
                            }

                            if ($logoPath) {
                                $html[] = Html::img($logoPath);
                            } else {
                                $html[] = '-';
                            }

                            return join('', $html);
                        }
                    ],
                ],
        ]) ?>
    </div>
</div>
