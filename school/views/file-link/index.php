<?php

/**
 * @var View $this
 * @var FileLink $model
 * @var ActiveDataProvider $dataProvider
 * @var FileLinkSearch $searchModel
 */

use common\components\helpers\IconHelper;
use school\models\Auth;
use school\models\FileLink;
use school\models\searches\FileLinkSearch;
use yii\bootstrap\Tabs;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Внешние ресурсы');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Documents'), 'url' => ['document/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Внешние ресурсы');

/** @var Auth $user */
$user   = Yii::$app->user->identity;
$roleId = $user->roleId;
$userId = $user->id;
$this->params['sidebar'] = ['model' => $model, 'roleId' => $roleId];

echo Tabs::widget([
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
]);
echo GridView::widget([
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
]);
