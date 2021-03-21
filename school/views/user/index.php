<?php

/**
 * @var View  $this
 * @var ActiveDataProvider $dataProvider
 * @var UserSearch $searchModel
 * @var array $offices
 * @var array $roles
 * @var array $statuses
 * @var array $urlParams
 * @var array $users
 */

use common\components\helpers\IconHelper;
use common\widgets\alert\AlertWidget;
use school\models\searches\UserSearch;
use school\models\User;
use school\widgets\userInfo\UserInfoWidget;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Users');
$this->params['breadcrumbs'][] = Yii::t('app','Users');
?>
<div class="row user-index">
    <div id="sidebar" class="col-xs-12 col-sm-12 col-md-2 col-lg-2 col-xl-2">
        <?= UserInfoWidget::widget() ?>
        <h4><?= Yii::t('app', 'Actions') ?>:</h4>
        <div class="form-group">
            <?= Html::a(
                    IconHelper::icon('plus') . ' ' . Yii::t('app', 'Add'),
                    ['user/create'],
                    ['class' => 'btn btn-success btn-sm btn-block']
            ) ?>
        </div>
        <div>
            <ul>
                <li>
                    Для создания пользователя с ролью "Преподаватель" <b>лучше</b> воспользоваться
                    <a href="/teacher/create"><b>этой</b></a> формой (используйте кнопку "Добавить нового").
                    Далее, вам потребуется вернуться в текущий раздел и задать пароль нового пользователя.
                </li>
            </ul>
        </div>
    </div>
    <div id="content" class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?= AlertWidget::widget() ?>
        <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'id',
                    'name',
                    'login',
                    'status' => [
                        'attribute' => 'status',
                        'filter' => $roles,
                        'value' => function(User $user) use ($roles) {
                            return $roles[$user->status] ?? '-';
                        },
                    ],
                    'calc_office' => [
                        'attribute' => 'calc_office',
                        'filter' => $offices,
                        'value' => function(User $user) use ($offices) {
                            return $offices[$user->calc_office] ?? '-';
                        },
                    ],
                    'visible' => [
                        'attribute' => 'visible',
                        'filter' => $statuses,
                        'value' => function(User $user) use ($statuses) {
                            return $statuses[$user->visible] ?? '-';
                        },
                    ],
                    [
                        'class' => ActionColumn::class,
                        'header' => Yii::t('app', 'Act.'),
                        'template' => '{view} {update} {toggle}',
                        'buttons' => [
                            'toggle' => function($url, $model, $key) {
                                if (intval($model->visible) === 1) {
                                    return Html::a(
                                        IconHelper::icon('times'),
                                        ['user/disable', 'id' => $model->id],
                                        ['title' => Yii::t('app', 'Disable user'), 'data-method' => 'post']
                                    );
                                } else {
                                    return Html::a(
                                        IconHelper::icon('check'),
                                        ['user/enable', 'id' => $model->id],
                                        ['title' => Yii::t('app', 'Enable user'), 'data-method' => 'post']
                                    );
                                }
                            }
                        ],
                    ],
                ],
        ]) ?>
    </div>
</div>
