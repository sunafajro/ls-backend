<?php

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var RoleSearch $searchModel
 * @var array $menuLinks
 */

use app\components\helpers\IconHelper;
use app\modules\school\models\search\RoleSearch;
use app\widgets\alert\AlertWidget;
use app\widgets\userInfo\UserInfoWidget;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Roles');
$this->params['breadcrumbs'][] = [ 'url' => ['admin/index'], 'label' => Yii::t('app','Administration')];
$this->params['breadcrumbs'][] = Yii::t('app','Roles');
?>
<div class="row  admin-roles">
    <div id="sidebar" class="col-xs-12 col-sm-12 col-md-2 col-lg-2 col-xl-2">
        <?= UserInfoWidget::widget() ?>
        <div class="dropdown">
            <?= Html::button(
                IconHelper::icon('book') . ' ' . Yii::t('app', 'Administration') . ' <span class="caret"></span>',
                [
                    'class' => 'btn btn-default dropdown-toggle btn-sm btn-block',
                    'type' => 'button',
                    'id' => 'dropdownMenu',
                    'data-toggle' => 'dropdown',
                    'aria-haspopup' => 'true',
                    'aria-expanded' => 'true',
                ]
            ) ?>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu">
              <?php foreach($menuLinks as $link) : ?>
                <li <?= $link['active'] ? 'class="active"' : '' ?>>
                    <?= Html::a($link['name'], [$link['url']], $link['classes'] ? ['class' => 'dropdown-item'] : '') ?>
                </li>
              <?php endforeach; ?>
            </ul>
        </div>
        <h4><?= Yii::t('app', 'Actions') ?></h4>
        <?= Html::a(IconHelper::icon('plus') . ' ' . Yii::t('app', 'Add'), ['role/create'], ['class' => 'btn btn-success btn-sm btn-block']) ?>
    </div>
    <div id="content" class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?= AlertWidget::widget() ?>
        <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'id',
                    'name',
                    [
                        'class' => ActionColumn::class,
                        'header' => Yii::t('app', 'Act.'),
                        'template' => '{update} {delete}',
                        'buttons' => [
                            'update' => function ($url, $model) {
                                return Html::a(
                                    IconHelper::icon('edit'),
                                    Url::to(['role/update', 'id' => $model->id])
                                );
                            },
                            'delete' => function ($url, $model) {
                                return Html::a(
                                    IconHelper::icon('trash'),
                                    Url::to(['role/delete', 'id' => $model->id]),
                                    ['data-method' => 'post', 'data-confirm' => 'Вы действительно хотите удалить роль?']
                                );
                            },
                        ],
                    ],
                ],
        ]) ?>
    </div>    
</div>