<?php

use app\models\Student;
use app\widgets\Alert;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View    $this
 * @var Student $model
 * @var array   $services
 * @var string  $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Students') . ' :: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];;
$this->params['breadcrumbs'][] = Yii::t('app', 'Settings');
?>
<div class="row row-offcanvas row-offcanvas-left student-settings">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?>
    </div>
    <div id="content" class="col-sm-10">
        <?= Alert::widget() ?>
        <h3>Видимость услуг в блоке "Количество оплаченных и учтенных занятий"</h3>
        <?= GridView::widget([
            'dataProvider' => $services,
            'columns' => [
                'name' => [
                    'attribute' => 'name',
                    'label'     => Yii::t('app', 'Service'),
                    'value'     => function (array $service) {
                        return "#{$service['id']} {$service['name']}";
                    }
                ],
                'num' => [
                    'attribute' => 'num',
                    'label'     => 'Доступно занятий',
                ],
                'visible' => [
                    'attribute' => 'visible',
                    'label'     => 'Видимый',
                    'value'     => function (array $service) {
                        return $service['visible'] ? 'Да' : 'Нет';
                    }
                ],
                [
                    'format' => 'raw',
                    'label'  => Yii::t('app', 'Act.'),
                    'value'  => function (array $service) use ($model) {
                        if ($service['visible']) {
                            return Html::a(
                                Html::tag('i', '', ['class' => 'fa fa-eye-slash', 'aria-hidden' => 'true']),
                                ['studname/update-settings', 'id' => $model->id],
                                [
                                    'style' => 'margin-right: 5px',
                                    'title' => 'Скрыть из карточки клиента',
                                    'data' => [
                                        'method'  => 'post',
                                        'params' => ['name' => 'serviceId', 'value' => $service['id'], 'action' => 'hide']
                                    ],
                                ]
                            );
                        } else {
                            return Html::a(
                                Html::tag('i', '', ['class' => 'fa fa-eye', 'aria-hidden' => 'true']),
                                ['studname/update-settings', 'id' => $model->id],
                                [
                                    'style' => 'margin-right: 5px',
                                    'title' => 'Показывать на карточке клиента',
                                    'data' => [
                                        'method'  => 'post',
                                        'params' => ['name' => 'serviceId', 'value' => $service['id'], 'action' => 'show']
                                    ],
                                ]
                            );
                        }
                    }
                ]
            ]
        ]) ?>
    </div>
</div>