<?php
/**
 * @var yii\web\View                $this
 * @var yii\widgets\ActiveForm      $form
 * @var app\models\Student          $student
 * @var app\models\Receipt          $receipt
 * @var app\models\Contract         $contract
 * @var array                       $columns
 * @var array                       $formReceiptData
 * @var yii\data\ActiveDataProvider $receipts
 * @var string                      $userInfoBlock
 */

use app\widgets\alert\AlertWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use yii\grid\GridView;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Receipts');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Clients'), 'url' => ['studname/index']];
$this->params['breadcrumbs'][] = ['label' => $student->name, 'url' => ['studname/view', 'id' => $student->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add receipt');

$columns = [];
$columns[] = [
    'class' => 'yii\grid\SerialColumn',
    'header' => '№',
    'headerOptions' => ['width' => '5%'],
];
$columns[] = [
    'attribute' => 'date',
    'format' => ['date', 'php:d.m.Y'],
    'headerOptions' => ['width' => '15%'],
    'label' => Yii::t('app', 'Date'),
];
$columns[] = [
    'attribute' => 'name',
    'format' => 'raw',
    'label' => Yii::t('app', 'Full name'),
];
$columns[] = [
    'attribute' => 'payer',
    'format' => 'raw',
    'label' => Yii::t('app', 'Payer'),
];
$columns[] = [
    'attribute' => 'purpose',
    'format' => 'raw',
    'headerOptions' => ['width' => '15%'],
    'label' => Yii::t('app', 'Destination'),
];
$columns[] = [
    'attribute' => 'sum',
    'format' => 'raw',
    'headerOptions' => ['width' => '10%'],
    'label' => Yii::t('app', 'Sum'),
    'value' => function ($receipt) {
        return substr($receipt['sum'] ?? '', 0, -2) . '.' . substr($receipt['sum'] ?? '', -2) . ' руб.';
    }
];
$columns[] = [
  'attribute' => 'userName',
  'format' => 'raw',
  'label' => Yii::t('app', 'Added by'),
];
if (((int)Yii::$app->session->get('user.ustatus') === 3 ||
   (int)Yii::$app->session->get('user.ustatus') === 4) &&
   (int)$student->active === 1) {
    $columns[] = [
        'class' => 'yii\grid\ActionColumn',
        'header' => Yii::t('app', 'Act.'),
        'headerOptions' => ['width' => '10%'],
        'template' => '{pdf}{delete}',
        'buttons' => [
            'pdf' => function ($url, $receipt) {
                return Html::a(
                    Html::tag('i',
                    '',
                    [
                        'class' => 'fa fa-print',
                        'aria-hidden' => true,
                    ]),
                    ['receipt/download-receipt', 'id' => $receipt['id']],
                    [
                        'class' => 'btn btn-default btn-xs',
                        'style' => 'margin-right: 0.2rem',
                        'target' => '_blank',
                    ]
                );
            },
            'delete' => function ($url, $receipt) {
                return Html::a(
                    Html::tag('i',
                    '',
                    [
                      'class' => 'fa fa-trash',
                      'aria-hidden' => true,
                    ]
                    ),
                    ['receipt/delete', 'id' => $receipt['id']],
                    [
                        'class' => 'btn btn-danger btn-xs',
                        'data' => [
                          'method' => 'post',
                        ],
                    ]
                );
            }
        ],
    ];
}
?>
<div class="row row-offcanvas row-offcanvas-left receipt-index">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
        <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?>
        <h4>Основные параметры:</h4>
        <?php foreach($formReceiptData as $row) { ?>
            <div class="form-group">
                <b><?= $row['title'] ?></b>
                <?= Html::input('text', '', $row['value'], ['class' => 'form-control', 'disabled' => true]) ?>
            </div>
        <?php } ?>
    </div>
    <div id="content" class="col-sm-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
            ]); ?>
        <?php } ?>
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle</button>
        </p>
        <?= AlertWidget::widget() ?>
        <?php
            $form = ActiveForm::begin([
                'method' => 'post',
                'action' => Url::to(['receipt/create', 'sid' => $student->id]),
            ]); ?>
            <div class="row">
                <div class="col-sm-1">
                    <?= Html::submitButton(
                            Html::tag('i', '', ['class' => 'fa fa-plus', 'aria-hidden' => 'true']),
                            ['class' => 'btn btn-success btn-block']
                        ) ?>   
                </div>
                <div class="col-sm-3">
                    <?= $form->field($receipt, 'name')
                            ->textInput([
                                'placeholder' => Yii::t('app', 'Student'),
                                'title' => Yii::t('app', 'Student'),
                            ])
                            ->label(false) ?>    
                </div>
                <div class="col-sm-3">
                    <?= $form->field($receipt, 'payer')
                            ->textInput([
                                'value' => $contract->signer ?? null,
                                'placeholder' => Yii::t('app', 'Payer'),
                                'title' => Yii::t('app', 'Payer'),
                            ])
                            ->label(false) ?>    
                </div>
                <div class="col-sm-3">
                    <?= $form->field($receipt, 'purpose')
                            ->textInput([
                                'placeholder' => Yii::t('app', 'Destination'),
                                'title' => Yii::t('app', 'Destination'),
                            ])
                            ->label(false) ?>
                </div>
                <div class="col-sm-2">
                    <?= $form->field($receipt, 'sum')
                            ->textInput([
                                'placeholder' => Yii::t('app', 'Sum'),
                                'title' => Yii::t('app', 'Sum'),
                            ])
                            ->label(false) ?>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
        <?= GridView::widget([
            'dataProvider' => $receipts,
            'layout'       => "{items}\n{pager}",
            'columns'      => $columns,
        ]) ?>
    </div>        
</div>
