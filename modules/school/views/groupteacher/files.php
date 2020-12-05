<?php

/**
 * @var View               $this
 * @var ActiveDataProvider $dataProvider
 * @var FileSearch         $searchModel
 * @var Groupteacher       $group
 * @var UploadForm         $uploadForm
 * @var int[]              $groupTeachers
 */

use app\components\helpers\IconHelper;
use app\models\Groupteacher;
use app\models\UploadForm;
use app\modules\school\models\Auth;
use app\modules\school\models\File;
use app\modules\school\models\search\FileSearch;
use app\widgets\alert\AlertWidget;
use app\widgets\groupInfo\GroupInfoWidget;
use app\widgets\groupMenu\GroupMenuWidget;
use app\widgets\userInfo\UserInfoWidget;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;

$this->title = Yii::$app->params['appTitle'] . ' Группа №' . $group->id;
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app','Group') . " №{$group->id}",
    'url' => ['groupteacher/view', 'id' => $group->id],
];
$this->params['breadcrumbs'][] = Yii::t('app', 'Files');

/** @var Auth $user */
$user      = Yii::$app->user->identity;
$roleId    = $user->roleId;
$userId    = $user->id;
$teacherId = $user->teacherId;
$canWrite  = in_array($user->roleId, [3, 4]) || in_array($user->teacherId, $groupTeachers);
?>
<div class="row row-offcanvas row-offcanvas-left group-announcements">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= UserInfoWidget::widget() ?>
        <?php if ($group->visible == 1) {
            echo GroupMenuWidget::widget([
                'activeItem' => 'files',
                'canCreate'  => in_array($user->roleId, [3, 4, 10]) || in_array($user->teacherId, $groupTeachers) || $user->id === 296,
                'groupId'    => $group->id,
            ]);
        } ?>
        <?php if ($canWrite) { ?>
            <h4><?= Yii::t('app', 'Actions') ?></h4>
            <?php $form = ActiveForm::begin([
                'method' => 'post',
                'action' => ['groupteacher/files', 'id' => $group->id],
                'options' => ['enctype' => 'multipart/form-data']
            ]); ?>
            <?= $form->field($uploadForm, 'file')->fileInput()->label(Yii::t('app','File')) ?>
            <div class="form-group">
                <?= Html::submitButton(
                    IconHelper::icon('upload') . ' ' . Yii::t('app','Upload'),
                    ['class' => 'btn btn-success btn-block']
                ) ?>
            </div>
            <?php ActiveForm::end(); ?>
        <?php } ?>
        <?= GroupInfoWidget::widget(['group' => $group]) ?>
    </div>
    <div class="col-sm-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
            ]); ?>
        <?php } ?>

        <div>
            <p class="visible-xs">
                <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">
                    <?= Yii::t('app', 'Toggle nav') ?>
                </button>
            </p>
        </div>

        <?= AlertWidget::widget() ?>
        <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel'  => $searchModel,
                'columns' => [
                    ['class' => SerialColumn::class],
                    'original_name' => [
                        'attribute' => 'original_name',
                        'format' => 'raw',
                        'value' => function (File $file) use ($group) {
                            return Html::a($file->original_name, [
                                'groupteacher/files',
                                'id'      => $group->id,
                                'action'  => 'download',
                                'file_id' => $file->id
                            ], ['target' => '_blank']);
                        }
                    ],
                    'size'     => [
                        'attribute' => 'size',
                        'format'    => ['shortSize', 2],
                    ],
                    'user_id'     => [
                        'attribute' => 'user_id',
                        'value' => function (File $file) {
                            $user = $file->user ?? null;
                            return $user->name ?? '';
                        }
                    ],
                    'create_date' => [
                        'attribute' => 'create_date',
                        'format' => ['date', 'php:d.m.Y'],
                    ],
                    [
                        'class' => ActionColumn::class,
                        'header' => Yii::t('app', 'Act.'),
                        'buttons' => [
                            'delete' => function ($url, File $file) use ($canWrite, $group) {
                                return Html::a(
                                   IconHelper::icon('trash'),
                                   ['groupteacher/files', 'id' => $group->id, 'action' => 'delete', 'file_id' => $file->id],
                                   [
                                       'title' => Yii::t('app', 'Delete'),
                                       'data' => [
                                           'method' => 'post',
                                           'confirm' => 'Вы действительно хотите удалить файл?',
                                       ],
                                   ]
                                );
                            }
                        ],
                        'template' => '{delete}',
                        'visible'  => $canWrite,
                    ],
                ],
        ]) ?>
    </div>
</div>