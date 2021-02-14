<?php

use school\models\search\OfficeBookSearch;
use common\widgets\alert\AlertWidget;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

/**
 * @var View               $this
 * @var ActiveDataProvider $dataProvider
 * @var OfficeBookSearch   $searchModel
 * @var array              $languages
 * @var array              $offices
 * @var array              $statuses
 * @var string             $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Book presence');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Books'), 'url' => ['book/index']];
$this->params['breadcrumbs'][] = Yii::t('app','Book presence');
?>
<div class="row row-offcanvas row-offcanvas-left office-book-index">
<div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?>
        <?= Html::tag('h4', Yii::t('app', 'Actions')) ?>
        <?= Html::a(
            Html::tag('i', '', ['class' => 'fa fa-plus', 'aria-hidden' => 'true']) .
            ' ' .
            Yii::t('app', 'Add'), ['office-book/create'], ['class' => 'btn btn-success btn-sm btn-block']) ?>
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
                    'serial_number' => [
                        'attribute' => 'serial_number',
                        'value' => function (array $book) {
                            return $book['serial_number'] ?? $book['id'];
                        }
                    ],
                    'name',
                    'author',
                    'isbn',
                    'year',
                    'language' => [
                        'attribute' => 'language',
                        'filter'    => $languages,
                        'value'     => function (array $book) use ($languages) {
                            return $languages[$book['language']] ?? $book['language']; 
                        },
                    ],
                    'office' => [
                        'attribute' => 'office',
                        'filter'    => $offices,                        
                        'value'     => function (array $book) use ($offices) {
                            return $offices[$book['office']] ?? $book['office']; 
                        },
                    ],
                    'status' => [
                        'attribute' => 'status',
                        'filter'    => $statuses,
                        'value'     => function (array $book) use ($statuses) {
                            return $statuses[$book['status']] ?? $book['status']; 
                        },
                    ],
                    'comment',
                    [
                        'format' => 'raw',
                        'label' => Yii::t('app', 'Act.'),
                        'value' => function (array $book) {
                            return join('', [
                                Html::a(
                                    Html::tag('i', '', ['class' => 'fa fa-pencil', 'aria-hidden' => 'true']),
                                    ['office-book/update', 'id' => $book['id']],
                                    ['class' => 'btn btn-xs btn-warning', 'style' => 'margin: 0 2px 2px 0']
                                ),
                                Html::a(
                                    Html::tag('i', '', ['class' => 'fa fa-trash', 'aria-hidden' => 'true']),
                                    ['office-book/delete', 'id' => $book['id']],
                                    ['class' => 'btn btn-xs btn-danger', 'data-method' => 'post', 'data-confirm' => 'Вы хотите удалить этот учебник?']
                                ),
                            ]);
                        }
                    ]
                ],
            ]) ?>
    </div>
</div>