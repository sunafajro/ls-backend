<?php

/**
 * @var View $this
 * @var UploadForm $uploadForm
 * @var ActiveDataProvider $dataProvider
 * @var DocumentSearch $searchModel
 */

use common\components\helpers\IconHelper;
use school\models\forms\UploadForm;
use school\models\Auth;
use school\models\Document;
use school\models\searches\DocumentSearch;
use yii\bootstrap\Tabs;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Documents');
$this->params['breadcrumbs'][] = Yii::t('app','Documents');

/** @var Auth $user */
$user   = Yii::$app->user->identity;
$roleId = $user->roleId;
$userId = $user->id;
$this->params['sidebar'] = ['uploadForm' => $uploadForm, 'roleId' => $roleId];

echo Tabs::widget([
    'items' => [
        [
            'label' => 'Документы',
            'url' => Url::to(['document/index']),
            'active' => true
        ],
        [
            'label' => 'Внешние ресурсы',
            'url' => Url::to(['file-link/index']),
            'active' => false,
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
            'value' => function (Document $file) {
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
            'value' => function (Document $file) {
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
                'delete' => function ($url, Document $file) use ($userId, $roleId) {
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
]);