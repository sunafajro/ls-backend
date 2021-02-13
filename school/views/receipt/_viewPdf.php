<?php

/**
 * @var yii\web\View $this
 * @var array $receipt
 */

use school\assets\PrintReceiptAsset;
use school\models\Receipt;
use yii\helpers\Html;
use Da\QrCode\QrCode;

PrintReceiptAsset::register($this);
$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Receipts');
$qrCode = isset($receipt['qrdata']) ? (new QrCode($receipt['qrdata'])) : NULL;
?>
<div class="body">
  <?php for($i = 0; $i < 2; $i++) { ?>
    <div class="main-block">
			<div class="left-block">
        <?php if ($i === 0) { ?>
          Извещение
        <?php } else { ?>
          Квитанция
          <?= $qrCode ? Html::img($qrCode->writeDataUri(), ['class' => 'qr-code-width', 'alt' => Yii::t('app', 'QR code')]) : NULL ?>
        <?php } ?>
			</div>
			<div class="right-block">
			    <div class="line-top-margin small-text-size">
						<div class="float-left left-half-width margin-left">ПАО СБЕРБАНК</div>
						<div class="float-left right-half-width margin-left">Форма №ПД-4</div>
						<div class="clearfix"></div>
					</div>
					<div class="line-top-margin">
						<div class="font-weight-bold right-block-line text-center">
							<?= Receipt::RECEIPT_COMPANY['value'] ?>
						</div>
						<div class="small-text-size text-center">(наименование получателя платежа)</div>
					</div>
					<div class="line-top-margin">
						<div class="font-weight-bold right-block-line">
							<div class="float-left font-weight-bold left-half-width margin-left">ИНН 2130122892 КПП 213001001</div>
							<div class="float-left font-weight-bold left-half-width margin-left">40702810075000008515</div>
							<div class="clearfix"></div>
						</div>
						<div class="small-text-size">
							<div class="float-left left-half-width margin-left">(инн получателя платежа)</div>
							<div class="float-left right-half-width margin-left">(номер счёта получателя платежа)</div>
							<div class="clearfix"></div>
						</div>
					</div>
					<div class="line-top-margin">
						<div class="font-weight-bold right-block-line text-center">
                            <?= Receipt::RECEIPT_BIC['title'] ?> <?= Receipt::RECEIPT_BIC['value'] ?> (<?= Receipt::RECEIPT_BANK_NAME['value'] ?>)
						</div>
						<div class="small-text-size text-center">
								(наименование банка получателя платежа)
						</div>
					</div>
					<div class="line-top-margin">
						<div class="font-weight-bold right-block-line text-center">
							<?= ($receipt['name'] ?? false) ? "ФИО: {$receipt['name']}; " : '' ?><?= $receipt['purpose'] ?? '' ?>
						</div>
						<div class="small-text-size text-center">
								(назначение платежа)
						</div>
					</div>
					<div class="line-top-margin">
						<div class="font-weight-bold right-block-line text-center">
							<?= $receipt['payer'] ?? '' ?>
						</div>
						<div class="small-text-size text-center">
								(Ф.И.О. плательщика)
						</div>
					</div>
					<div class="line-top-margin">
						<div class="sum-text">
							<?php if (!isset($receipt['sum']) || (isset($receipt['sum']) && $receipt['sum'] === '')) {
								echo '';
							} else {
							    $num = explode('.', number_format((float)$receipt['sum'], 2, '.', ''));
								echo $num[0] . ' руб. ' . $num[1] . ' коп.';
							} ?>
						</div>
						<div class="small-text-size text-center">
								(сумма платежа)
						</div>
					</div>
					<div class="line-top-margin small-text-size line-bottom-padding">
						<div class="margin-left">С условиями приёма указанной в платёжном документе суммы, в т.ч. с суммой взимаемой платы за услуги</div>
						<div>
							<div class="float-left left-half-width margin-left">банка, ознакомлен и согласен.</div>
							<div class="float-left right-half-width margin-left">Подпись плательщика ______________\</div>
							<div class="clearfix"></div>
						</div>
					</div>
			</div>
			<div class="clearfix"></div>
    </div>
  <?php } ?>
</div>
