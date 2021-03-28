<?php
/**
 * @var yii\web\View                $this
 * @var yii\widgets\ActiveForm      $form
 * @var school\models\Student       $student
 * @var school\models\Receipt       $receipt
 * @var school\models\Contract      $contract
 * @var array                       $columns
 * @var array                       $formReceiptData
 * @var yii\data\ActiveDataProvider $receipts
 * @var string                      $userInfoBlock
 */

use school\models\Auth;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\grid\GridView;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app', 'Receipts');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Clients'), 'url' => ['studname/index']];
$this->params['breadcrumbs'][] = ['label' => $student->name, 'url' => ['studname/view', 'id' => $student->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Create receipt');

$this->params['sidebar'] = ['formReceiptData' => $formReceiptData];

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
/** @var Auth $user */
$user   = Yii::$app->user->identity;
$roleId = $user->roleId;
if (in_array($roleId, [3, 4]) && (int)$student->active === 1) {
    $columns[] = [
        'class' => 'yii\grid\ActionColumn',
        'header' => Yii::t('app', 'Act.'),
        'headerOptions' => ['width' => '10%'],
        'template' => '{pdf} {delete}',
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
                    ->Input('number', [
                        'step' => '0.01',
                        'placeholder' => Yii::t('app', 'Sum'),
                        'title' => Yii::t('app', 'Sum'),
                    ])
                    ->label(false) ?>
        </div>
    </div>
<?php ActiveForm::end();
echo GridView::widget([
    'dataProvider' => $receipts,
    'layout'       => "{items}\n{pager}",
    'columns'      => $columns,
]);
