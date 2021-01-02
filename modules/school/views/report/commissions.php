<?php

use app\models\StudentCommission;
use app\widgets\alert\AlertWidget;
use app\widgets\filters\FiltersWidget;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/**
 * @var View               $this
 * @var StudentCommission  $searchModel
 * @var ActiveDataProvider $dataProvider
 * @var string             $actionUrl
 * @var string             $end
 * @var array              $offices
 * @var array              $reportList
 * @var string             $start
 * @var string             $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Reports');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Reports'), 'url' => ['report/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Commissions');
?>
<div class="report-commissions">
    <?= $this->render('_sidebar', [
            'actionUrl' => $actionUrl,
            'hints'     => [
                'При фильтрации по столбцу Дата, фильтр по периоду игнорируется.',
            ],
            'items' => [
                [
                    'name'  => 'start',
                    'title' => 'Начало периода',
                    'type'  => FiltersWidget::FIELD_TYPE_DATE_INPUT,
                    'value' => $start ?? '',
                ],
                [
                    'name'  => 'end',
                    'title' => 'Конец периода',
                    'type'  => FiltersWidget::FIELD_TYPE_DATE_INPUT,
                    'value' => $end ?? '',
                ],
            ],
            'reportList' => $reportList ?? [],
    ]) ?>
    <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') {
            try {
                echo Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
                ]);
            } catch (Exception $e) {
                echo Html::tag('div', 'Не удалось отобразить виджет. ' . $e->getMessage(), ['class' => 'alert alert-danger']);
            }
        } ?>
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>
        <?php
            try {
                echo AlertWidget::widget();
            } catch (Exception $e) {
                echo Html::tag('div', 'Не удалось отобразить виджет. ' . $e->getMessage(), ['class' => 'alert alert-danger']);
            }
        ?>
        <?php
            try {
                echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel'  => $searchModel,
                    'layout'       => "{pager}\n{items}\n{pager}",
                    'columns'      => [
                        'id' => [
                            'attribute' => 'id',
                            'headerOptions' => ['width' => '5%'],
                            'label' => '№',
                        ],
                        'date' => [
                            'attribute' => 'date',
                            'format'    => ['date', 'php:d.m.Y'],
                            'headerOptions' => ['width' => '5%'],
                            'label'     => Yii::t('app', 'Date'),
                        ],
                        'studentName' => [
                            'attribute' => 'studentName',
                            'format'    => 'raw',
                            'headerOptions' => ['width' => '15%'],
                            'label'     => Yii::t('app', 'Student'),
                            'value'     => function ($model) {
                                return Html::a($model['studentName'], ['studname/view', 'id' => $model['studentId']]);
                            },
                        ],
                        'value' => [
                            'attribute' => 'value',
                            'format' => 'raw',
                            'headerOptions' => ['width' => '15%'],
                            'label'     => Yii::t('app', 'Sum'),
                            'value'     => function ($model) {
                                $value = [];
                                $value[] = number_format($model['value'] ?? 0, 2, '.', ' ') . ' руб.';
                                if ($model['percent'] > 0) {
                                    $value[] = Html::tag('small', "{$model['percent']}% от долга {$model['debt']} руб.");
                                }
                                return join(Html::tag('br'), $value);
                            },
                        ],
                        'comment' => [
                            'attribute' => 'comment',
                            'headerOptions' => ['width' => '15%'],
                            'label'     => Yii::t('app', 'Comment'),
                        ],
                        'userName' => [
                            'attribute' => 'userName',
                            'format'    => 'raw',
                            'headerOptions' => ['width' => '15%'],
                            'label'     => Yii::t('app', 'Created by'),
                            'value'     => function ($model) {
                                return $model['userName'];
                            },
                        ],
                        'officeId' => [
                            'attribute' => 'officeId',
                            'filter' => $offices,
                            'headerOptions' => ['width' => '15%'],
                            'label' => Yii::t('app', 'Office'),
                            'value' => function (array $model) use ($offices) {
                                return $offices[$model['officeId']] ?? $model['officeId'];
                            },
                            'visible' => in_array((int)Yii::$app->session->get('user.ustatus'), [3, 8]),
                        ],
                    ],
                ]);
            } catch (Exception $e) {
                echo Html::tag('div', 'Не удалось отобразить виджет. ' . $e->getMessage(), ['class' => 'alert alert-danger']);
            }
         ?>
    </div>
</div>