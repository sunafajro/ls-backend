<?php
/**
 * @var View                  $this
 * @var ActiveDataProvider    $dataProvider
 * @var Salestud              $model
 * @var Student               $student
 * @var StudentDiscountSearch $searchModel
 * @var array                 $sales
 * @var string                $userInfoBlock
 */

use school\models\Sale;
use school\models\Salestud;
use school\models\searches\StudentDiscountSearch;
use school\models\Student;
use common\widgets\alert\AlertWidget;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

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

        <?= AlertWidget::widget() ?>

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
                        /** @var Student|null $student */
                        $student = Student::find()->andWhere(['id' => $discount['studentId']])->one();
                        $sales = [];
                        foreach ($student->getStudentSales() as $sale) {
                            if ((int)$discount['id'] !== (int)$sale['id']) {
                                $sales[] = "{$sale['name']}";
                            }
                        }
                        $elementId = "js--student-sales-collapse-{$discount['studentId']}-{$discount['id']}";
                        $result = [
                            Html::tag(
                                    'div',
                                Html::a(Html::tag('i', '', ['class' => 'fa fa-user']), ['studname/view', 'id' => $discount['studentId']], ['title' => 'Перейти в профиль'])
                                . ' '
                                . (!empty($sales)
                                    ? Html::a($discount['student'], "#{$elementId}", ['role' => 'button', 'data-toggle' => 'collapse', 'expanded' => 'false', 'aria-controls' => $elementId])
                                    : $discount['student'])
                            ),
                            !empty($sales)
                                ? Html::tag(
                                        'div',
                                        Html::tag('div', join(Html::tag('br'), $sales), ['class' => 'well small', 'style' => 'margin-bottom:0;padding:5px']),
                                        ['class' => 'collapse', 'id' => $elementId]
                                  )
                                : '',
                        ];
                        return join('', $result);
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
                'reason' => [
                    'attribute' => 'reason',
                    'label'     => Yii::t('app', 'Reason'),
                ],
                'actions' => [
                    'attribute' => 'actions',
                    'format'    => 'raw',
                    'label'     => Yii::t('app', 'Act.'),
                    'value'     => function (array $discount) {
                        $buttons = [
                            Html::a(
                                Html::tag('span', null, ['class' => 'fa fa-check', 'aria-hidden' => true]),
                                ['salestud/approve', 'id' => $discount['id'], 'status' => 'accept'],
                                [
                                    'class' => 'btn btn-success btn-xs',
                                    'data' => [
                                        'method' => 'POST',
                                    ],
                                    'style' => 'margin-right: 5px',
                                    'title' => Yii::t('app', 'Approve discount'),
                                ]
                            ),
                            Html::a(
                                Html::tag('span', null, ['class' => 'fa fa-times', 'aria-hidden' => true]),
                                ['salestud/approve', 'id' => $discount['id'], 'status' => 'refuse'],
                                [
                                    'class' => 'btn btn-danger btn-xs',
                                    'data' => [
                                        'method' => 'POST',
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