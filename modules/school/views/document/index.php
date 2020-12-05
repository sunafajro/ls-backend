<?php

/**
 * @var View               $this
 * @var UploadForm         $uploadForm
 * @var ActiveDataProvider $dataProvider
 * @var FileSearch         $searchModel
 */

use app\components\helpers\IconHelper;
use app\models\UploadForm;
use app\modules\school\models\Auth;
use app\modules\school\models\File;
use app\modules\school\models\search\FileSearch;
use app\widgets\userInfo\UserInfoWidget;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use app\widgets\alert\AlertWidget;
use yii\widgets\Breadcrumbs;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Documents');
$this->params['breadcrumbs'][] = Yii::t('app','Documents');

/** @var Auth $user */
$user   = Yii::$app->user->identity;
$roleId = $user->roleId;
$userId = $user->id;
?>
<div class="row row-offcanvas row-offcanvas-left document-index">
    <div id="sidebar" class="col-xs-12 col-sm-12 col-md-2 col-lg-2 col-xl-2">
        <?= UserInfoWidget::widget() ?>
        <?php if (in_array($roleId, [3, 4])) { ?>
            <h4><?= Yii::t('app', 'Actions') ?></h4>
            <?php $form = ActiveForm::begin([
                'method' => 'post',
                'action' => ['document/upload'],
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
	</div>
	<div id="content" class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?= AlertWidget::widget() ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'columns' => [
                ['class' => SerialColumn::class],
                'original_name' => [
                    'attribute' => 'original_name',
                    'format' => 'raw',
                    'value' => function (File $file) {
                        return Html::a($file->original_name, [
                            'document/download',
                            'id'      => $file->id,
                        ], ['target' => '_blank']);
                    }
                ],
                'size' => [
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
                        'delete' => function ($url, File $file) use ($userId, $roleId) {
                            $canWrite = in_array($roleId, [3]) || $file->user_id === $userId;
                            return $canWrite ? Html::a(
                                IconHelper::icon('trash'),
                                ['document/delete', 'id' => $file->id],
                                [
                                    'title' => Yii::t('app', 'Delete'),
                                    'data' => [
                                        'method' => 'post',
                                        'confirm' => 'Вы действительно хотите удалить файл?',
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