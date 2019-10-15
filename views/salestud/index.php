<?php

use app\models\Sale;
use app\models\Salestud;
use app\models\search\StudentDiscountSearch;
use app\models\Student;
use app\widgets\Alert;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/**
 * @var ActiveDataProvider    $dataProvider
 * @var Salestud              $model
 * @var Student               $student
 * @var StudentDiscountSearch $searchModel
 * @var View                  $this
 * @var array                 $sales
 * @var string                $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Approve discounts');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Discounts'), 'url' => ['sale/index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Approve discounts');
?>
<div class="row row-offcanvas row-offcanvas-left discount-index">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?>
        <ul style="margin-top: 1rem">
            <li>Подтвержденная скидка остается действующей.</li>
            <li>Отклоненная скидка, аннулируется (счет в котором использовалась скидка остается без изменений).</li>
        </ul>
    </div>
    <div class="col-sm-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
            ]); ?>
        <?php } ?>

        <p class="pull-left visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>

        <?= Alert::widget() ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'layout'       => "{pager}\n{items}\n{pager}",
            'columns'      => [
                'date' => [
                    'attribute' => 'date',
                    'value' => function (array $discount) {
                        return date('d.m.Y', strtotime($discount['date']));
                    }
                ],
                'student' => [
                    'attribute' => 'student',
                    'format'    => 'raw',
                    'value'     => function (array $discount) {
                        return Html::a($discount['student'], ['studname/view', 'id' => $discount['studentId']]);
                    }
                ],
                'discount' => [
                    'attribute' => 'discount',
                    'format'    => 'raw',
                    'value'     => function (array $discount) {
                        $d = Sale::findOne($discount['discountId']);
                        $result = '';
                        if (!empty($d)) {
                            $result = $d->value . ($d->procent === Sale::TYPE_RUB ? ' руб.' : '%');
                        }

                        return $result;
                    }
                ],
                'user' => [
                    'attribute' => 'user',
                ],
                'actions' => [
                    'attribute' => 'actions',
                    'format'    => 'raw',
                    'label'     => Yii::t('app', 'Act.'),
                    'value'     => function (array $discount) {
                        $buttons = [
                            Html::a(
                                Html::tag('span', null, ['class' => 'fa fa-check', 'aria-hidden' => true]),
                                ['salestud/approve'],
                                [
                                    'class' => 'btn btn-success btn-xs',
                                    'data' => [
                                        'method' => 'POST',
                                        'params' => [
                                            'id' => $discount['id'],
                                            'status' => 'accept',
                                        ],
                                    ],
                                    'style' => 'margin-right: 5px',
                                    'title' => Yii::t('app', 'Approve discount'),
                                ]
                            ),
                            Html::a(
                                Html::tag('span', null, ['class' => 'fa fa-times', 'aria-hidden' => true]),
                                ['salestud/approve'],
                                [
                                    'class' => 'btn btn-danger btn-xs',
                                    'data' => [
                                        'method' => 'POST',
                                        'params' => [
                                            'id' => $discount['id'],
                                            'status' => 'refuse',
                                        ],
                                    ],
                                    'title' => Yii::t('app', 'Refuse discount'),
                                ]
                            ),
                        ];

                        return join('', $buttons);
                    }
                ],
            ]
        ]) ?>
    </div>
</div>