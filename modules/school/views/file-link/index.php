<?php

/**
 * @var View $this
 * @var FileLink $model
 * @var ActiveDataProvider $dataProvider
 * @var FileLinkSearch $searchModel
 */

use app\components\helpers\IconHelper;
use app\modules\school\models\Auth;
use app\modules\school\models\FileLink;
use app\modules\school\models\search\FileLinkSearch;
use app\widgets\userInfo\UserInfoWidget;
use yii\bootstrap\Tabs;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
use app\widgets\alert\AlertWidget;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Внешние ресурсы');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Documents'), 'url' => ['document/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Внешние ресурсы');

/** @var Auth $user */
$user   = Yii::$app->user->identity;
$roleId = $user->roleId;
$userId = $user->id;
?>
<div class="row document-index">
    <div id="sidebar" class="col-xs-12 col-sm-12 col-md-2 col-lg-2 col-xl-2">
        <?= UserInfoWidget::widget() ?>
        <?php if (in_array($roleId, [3, 4])) { ?>
            <h4><?= Yii::t('app', 'Actions') ?></h4>
            <?php $form = ActiveForm::begin([
                'method' => 'post',
                'action' => ['file-link/create'],
            ]); ?>
            <?= $form->field($model, 'file_name')->textInput() ?>
            <?= $form->field($model, 'original_name')->textInput() ?>
            <div class="form-group">
                <?= Html::submitButton(
                    IconHelper::icon('upload') . ' ' . Yii::t('app','Create'),
                    ['class' => 'btn btn-success btn-block']
                ) ?>
            </div>
            <?php ActiveForm::end(); ?>
        <?php } ?>
    </div>
    <div id="content" class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?= AlertWidget::widget() ?>

        <?= Tabs::widget([
            'items' => [
                [
                    'label' => 'Документы',
                    'url' => Url::to(['document/index']),
                    'active' => false,
                ],
                [
                    'label' => 'Внешние ресурсы',
                    'url' => Url::to(['file-link/index']),
                    'active' => true,
                ],
            ],
        ]) ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'columns' => [
                ['class' => SerialColumn::class],
                'original_name' => [
                    'attribute' => 'original_name',
                    'format' => 'raw',
                    'value' => function (FileLink $file) {
                        return Html::a($file->original_name, $file->file_name, ['target' => '_blank']);
                    }
                ],
                'user_id'     => [
                    'attribute' => 'user_id',
                    'value' => function (FileLink $file) {
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
                        'delete' => function ($url, FileLink $file) use ($userId, $roleId) {
                            $canWrite = in_array($roleId, [3]) || $file->user_id === $userId;
                            return $canWrite ? Html::a(
                                IconHelper::icon('trash'),
                                ['file-link/delete', 'id' => $file->id],
                                [
                                    'title' => Yii::t('app', 'Delete'),
                                    'data' => [
                                        'method' => 'post',
                                        'confirm' => 'Вы действительно хотите удалить ссылку?',
                                    ],
                                ]
                            ) : '';
                        }
                    ],
                    'template' => '{delete}',
                ],
            ],
        ]) ?>
    </div>
</div>
