<?php

/**
 * @var View $this
 * @var User $user
 * @var UserTimeTrackingForm $model
 * @var UploadForm $imageForm
 * @var array $can
 */

use common\components\helpers\IconHelper;
use school\assets\UserViewAsset;
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

$this->params['sidebar'] = ['viewFile' => '//user/sidebars/_view', 'params' => ['can' => $can, 'user' => $user]];

if ($can['updateUser']) { ?>
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
                'value' => function(User $user) use ($can, $imageForm) {
                    $logoPath = $user->getImageWebPath();
                    $html = [];
                    if ($can['updateImage']) {
                        $html[] = $this->render('_upload', ['imageForm' => $imageForm, 'user' => $user]);
                    }

                    $html[] = Html::img($logoPath, ['class' => 'thumbnail', 'style' => 'margin-bottom:1rem;max-height:300px']);
                    if ($can['updateImage']) {
                        $html[] = Html::a(
                            IconHelper::icon('trash') . ' ' . Yii::t('app', 'Delete'),
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
