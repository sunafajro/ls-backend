<?php

/**
 * @var View                  $this
 * @var Book                  $book
 * @var BookOrder             $bookOrder
 * @var BookOrderPosition     $model
 * @var BookOrderPositionItem $itemModel
 * @var array                 $offices
 */

use app\models\Book;
use app\models\BookOrder;
use app\models\BookOrderPosition;
use app\models\BookOrderPositionItem;
use app\widgets\Alert;
use app\widgets\autocomplete\AutoCompleteWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;

$title = Yii::t('app','Edit position');
$this->title = Yii::$app->params['appTitle'] . $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Books'), 'url' => ['book/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Book order') . ' №' . $bookOrder->id, 'url' => ['book-order-position/index', 'id' => $bookOrder->id]];
$this->params['breadcrumbs'][] = $title;

$paymentTypes = $itemModel->getPaymentTypes();
?>
<div class="row row-offcanvas row-offcanvas-left book-order">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?? '' ?>
        <h4>Заказ №<?= $bookOrder->id ?></h4>
        <p><b>Дата начала:</b> <?= date('d.m.Y', strtotime($bookOrder->date_start)) ?></p>
            <p><b>Дата окончания:</b> <?= date('d.m.Y', strtotime($bookOrder->date_end)) ?></p>
            <p><b>Количество позиций:</b> <?= Html::a(
                    $bookOrder->positionsCount ?? 0,
                    ['book-order-position/index', 'id' => $bookOrder->id]
                ) ?></p>

        <h4 style="margin-top:20px">Распределить на студента:</h4>
        <?php $form = ActiveForm::begin([
                'action' => ['book-order-position/change-items', 'id' => $model->id, 'action' => 'create']
        ]) ?>
            <?= AutoCompleteWidget::widget([
                    'hiddenField' => [
                        'name' => Html::getInputName($itemModel, 'student_id'),
                    ],
                    'searchField' => [
                        'error' => null,
                        'label' => Yii::t('app', 'Student'),
                        'name' => Html::getInputName($itemModel, 'student_name'),
                        'url'   => Url::to(['book-order-position/autocomplete', 'id' => $model->id]),
                        'minLength' => 3,
                    ]
            ]) ?>
            <?= $form->field($itemModel, 'count')->input('number') ?>
            <?= $form->field($itemModel, 'payment_type')->dropDownList($paymentTypes) ?>
            <?= $form->field($itemModel, 'payment_comment')->textarea(['rows' => 3]) ?>
            <?= Html::submitButton(Yii::t('app', 'Add'), ['class' => 'btn btn-sm btn-success']) ?>
        <?php ActiveForm::end() ?>
    </div>
    <div class="col-sm-6">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
            ]); ?>
        <?php } ?>

        <p class="pull-left visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>

        <?= Alert::widget() ?>
        
        <?= $this->render('_form', [
                'book'    => $book,
                'model'   => $model,
                'offices' => $offices,
        ]) ?>
        <table class="table">
            <thead>
                <tr>
                    <td><?= Yii::t('app', 'Student') ?></td>
                    <td><?= Yii::t('app', 'Count') ?></td>
                    <td><?= Yii::t('app', 'Paid') ?></td>
                    <td><?= Yii::t('app', 'Payment type') ?></td>
                    <td><?= Yii::t('app', 'Payment comment') ?></td>
                </tr>
            </thead>
            <tbody>
                <?php
                    /** @var BookOrderPositionItem $item */
                    foreach($model->getBookOrderPositionItems()->andWhere(['visible' => 1])->all() ?? [] as $item) { ?>
                    <tr>
                        <td><?= $item->student_name ?></td>
                        <td><?= $item->count ?></td>
                        <td><?= $item->paid ?></td>
                        <td>
                            <?= $paymentTypes[$item->payment_type] ?? $item->payment_type ?>
                        </td>
                        <td><?= Html::encode($item->payment_comment) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>