<?php

/**
 * @var View $this
 * @var User $user
 * @var UserTimeTrackingForm $model
 * @var UploadForm $imageForm
 */

use common\components\helpers\IconHelper;
use school\assets\UserViewAsset;
use school\models\AccessRule;
use school\models\forms\UploadForm;
use school\models\User;
use school\models\forms\UserTimeTrackingForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\DetailView;

$this->title = Yii::$app->name . ' :: ' .  $user->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $user->name;

UserViewAsset::register($this);

$this->params['sidebar'] = ['user' => $user];

$canChangePassword = AccessRule::checkAccess('user_change-password');
$canUpdate = AccessRule::checkAccess('user_update');
$canRestore = AccessRule::checkAccess('user_restore');
$canRemove = AccessRule::checkAccess('user_remove');
$canUploadImage = AccessRule::checkAccess('user_upload-image');
$canDeleteImage = AccessRule::checkAccess('user_delete-image');
$canTimeTracking = AccessRule::checkAccess('user_time-tracking');

if ($canUpdate || $canRemove || $canRestore) { ?>
    <div style="margin-bottom: 1rem">
        <?php
            if ($canUpdate) {
                echo Html::a(
                    IconHelper::icon('edit', Yii::t('app', 'Update')),
                    ['user/update', 'id' => $user->id],
                    ['class' => 'btn btn-info', 'style' => 'margin-right:1rem']
                );
            }
            if ($canRemove && (int)$user->visible === 1) {
                echo Html::a(
                    IconHelper::icon('times', Yii::t('app', 'Disable')),
                    ['user/remove', 'id' => $user->id],
                    ['class' => 'btn btn-danger', 'style' => 'margin-right:1rem']
                );
            }
            if ($canRestore && (int)$user->visible === 0) {
                echo Html::a(
                    IconHelper::icon('check', Yii::t('app', 'Enable')),
                    ['user/restore', 'id' => $user->id],
                    ['class' => 'btn btn-success']
                );
            }
        ?>
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
                'value' => function(User $user) use ($canChangePassword) {
                    $html = [];
                    if ($canChangePassword) {
                        $html[] = Html::button(
                            IconHelper::icon('edit', Yii::t('app', 'Change')),
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
                'value' => function(User $user) use ($canUploadImage, $canDeleteImage, $imageForm) {
                    $logoPath = $user->getImageWebPath();
                    $html = [];
                    if ($canUploadImage) {
                        $html[] = $this->render('_upload', ['imageForm' => $imageForm, 'user' => $user]);
                    }

                    $html[] = Html::img($logoPath, ['class' => 'thumbnail', 'style' => 'margin-bottom:1rem;max-height:300px']);
                    if ($canDeleteImage) {
                        $html[] = Html::a(
                            IconHelper::icon('trash', Yii::t('app', 'Delete')),
                            ['user/delete-image', 'id' => $user->id],
                            [
                                'class' => 'btn btn-danger btn-xs',
                                'data-method' => 'POST',
                                'data-confirm' => Yii::t('app', 'Do you really want to delete the image?'),
                            ]
                        );
                    }

                    return join('', $html);
                }
            ],
        ],
]) ?>
