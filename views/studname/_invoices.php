<?php

/**
 * @var array $invcount
 * @var array $invoices
 */

use Yii;
use app\models\Invoicestud;
use yii\helpers\Html;

$invtype = [1 => 'In work', 2 => 'Done', 3 => 'Canceled'];
$invoicesum = 0;
$invoicedone = 0;
$invoiceall = 0;

function calculateSales(array $invoice): string
{
	$sales = [];
	if ($invoice['perm_sale']) {
		$sales[] = $invoice['perm_sale'];
	}
	if ($invoice['rub_sale']) {
	   $sales[] = $invoice['rub_sale'];
	}
	if ($invoice['proc_sale']) {
		$sales[] = $invoice['proc_sale'];
	}
	return implode(', ', $sales);
}

for ($i = 1; $i <= 3; $i++) { ?>
    <?php if ($invcount[$i] > 0) { ?>
        <div class="panel panel-default">
			<div class="panel-heading" role="tab" id="collapseInvoiceGroupHeading<?= $i ?>">
			    <h4 id="-collapsible-list-group-" class="panel-title">
			        <?php if ($i === 1) { ?>
			            <button type="button" class="btn btn-xs btn-default" data-container="body" data-toggle="popover" data-placement="top" data-content="Менеджеры, не забывайте отслеживать отработанные счета и вовремя ставить отметку 'отработанный'">
							<?= Html::tag('i', '', ['class' => 'glyphicon glyphicon-info-sign', 'aria-hidden' => 'true']) ?>
						</button>
					<?php } ?>			        
                    <?= Html::a(
						Yii::t('app', $invtype[$i]) . ' (' . $invcount[$i] . ')',
						['#collapseInvoiceGroup' . $i],
						[
							'aria-controls' => 'collapseInvoiceGroup' . $i,
							'aria-expanded' => $i === 1 ? 'true' : 'false',
							'class' => $i === 1 ? '' : 'collapsed',
							'data-toggle' => 'collapse',
						])
					?>
			    </h4>
		    </div>
			<div aria-expanded="<?= $i === 1 ? 'true' : 'false' ?>" id="collapseInvoiceGroup<?= $i ?>" class="panel-collapse collapse <?= $i === 1 ? 'in' : '' ?>" role="tabpanel" aria-labelledby="collapseInvoiceGroupHeading<?= $i ?>">
			    <ul class="list-group">
				<?php foreach ($invoices as $invoice) { ?>
					<?php if ($i === 1 && (int)$invoice['idone'] === 0 && (int)$invoice['ivisible'] === 1) { ?>
						<li class="list-group-item list-group-item-warning">
						<?= (int)$invoice['remain'] === Invoicestud::TYPE_REMAIN ? '(остаточный)' : '' ?>
						<?= (int)$invoice['remain'] === Invoicestud::TYPE_NETTING ? '(взаимозачет)' : '' ?>
						<?= $invoice['sname'] ?> (услуга#<?= $invoice['sid'] ?> от <?= date('d.m.y', strtotime($invoice['idate'])) ?>) <?= $invoice['inum'] ?> зан. на сумму <?= $invoice['ivalue'] ?> р.<br />
						<?php if ($invoice['perm_sale'] || $invoice['rub_sale'] || $invoice['proc_sale']) { ?>
							<small>
								<em>Учтены скидки: <?= calculateSales($invoice) ?></em>
							</small>
						<br />
						<?php } ?>
						<?php
						    $actions = [
								Html::a(Yii::t('app', 'Cancel'), ['invoice/toggle', 'id' => $invoice['iid'], 'action' => 'enable'])
							];
							if ((int)$invoice['remain'] === Invoicestud::TYPE_REMAIN) {
								$actions[] = Html::a(Yii::t('app', 'Unset remain'), [
									'invoice/toggle', 'id' => $invoice['iid'], 'action' => 'remain'
								]);
							} else if ((int)$invoice['remain'] === Invoicestud::TYPE_NETTING) {
								$actions[] = Html::a(Yii::t('app', 'Unset netting'), [
									'invoice/toggle', 'id' => $invoice['iid'], 'action' => 'netting'
								]);
							} else if ((int)$invoice['remain'] === Invoicestud::TYPE_NORMAL) {
								$actions[] = Html::a(Yii::t('app', 'Set remain'), ['invoice/toggle', 'id' => $invoice['iid'], 'action' => 'remain']);
								$actions[] = Html::a(Yii::t('app', 'Set netting'), ['invoice/toggle', 'id' => $invoice['iid'], 'action' => 'netting']);
							}
							$actions[] = Html::a((int)$invoice['oid'] !== 6 ? Yii::t('app', 'Set corporative') : Yii::t('app', 'Unset corporative'), ['invoice/toggle', 'id' => $invoice['iid'], 'action' => 'corp']);
							$actions[] = Html::a(Yii::t('app', 'Set done'), ['invoice/toggle', 'id' => $invoice['iid'], 'action' => 'done']);
						?>
						<?= implode(' | ', $actions) ?>
						<br />
						<small>
						    офис: <?= $invoice['oname'] ?><br />
						    кем выдан счёт: <?= $invoice['uname'] ?>
						</small></li>
					<?php
					    $invoicesum += $invoice['ivalue'];
						$invoiceall += 1;
					}
					if ($i === 2 && (int)$invoice['idone'] === 1 && (int)$invoice['ivisible'] === 1) { ?>
						<li class="list-group-item list-group-item-success">
						<?= $invoice['sname'] ?> (услуга#<?= $invoice['sid'] ?> от <?= date('d.m.y', strtotime($invoice['idate']))?>) <?= $invoice['inum'] ?> зан. на сумму <?= $invoice['ivalue'] ?> р.<br />
						<?php if ($invoice['perm_sale'] || $invoice['rub_sale'] || $invoice['proc_sale']) { ?>
							<small>
								<em>Учтены скидки: <?= calculateSales($invoice) ?></em>
							</small>
						<br />
						<?php } ?>
						<?= Html::a(Yii::t('app', 'Unset done'), ['invoice/toggle', 'id' => $invoice['iid'], 'action' => 'done']) ?>
						<br />
						<small>
						    офис: <?= $invoice['oname'] ?><br />
						    кем выдан счёт: <?= $invoice['uname'] ?><br/>
						    (отработан: <?= date('d.m.y', strtotime($invoice['ddone'])) ?>, кем: <?= $invoice['udone'] ?>)
						</small>
						</li>
					<?php
					    $invoicesum += $invoice['ivalue'];
						$invoicedone += 1;
						$invoiceall += 1;
					}
					// распечатываем счета - Аннулированные
					if ($i === 3 && (int)$invoice['ivisible'] === 0) { ?>
						<li class="list-group-item list-group-item-danger">
						<?= $invoice['sname'] ?> (услуга#<?= $invoice['sid'] ?> от <?= date('d.m.y', strtotime($invoice['idate'])) ?>) <?= $invoice['inum'] ?> зан. на сумму <?= $invoice['ivalue'] ?> р.<br />
						<?php if ($invoice['perm_sale'] || $invoice['rub_sale'] || $invoice['proc_sale']) { ?>
							<small>
								<em>Учтены скидки: <?= calculateSales($invoice) ?></em>
							</small>
						<br />
						<?php } ?>
						<?php
						    $actions = [Html::a(Yii::t('app', 'Unset done'), ['invoice/toggle', 'id' => $invoice['iid'], 'action' => 'enable'])];
						    if ((int)Yii::$app->session->get('user.ustatus') === 3) {
							    $actions[] = Html::a(Yii::t('app', 'Delete'), ['invoice/delete','id' => $invoice['iid']]);
							}
						?>
						<?= implode(' | ', $actions) ?>
						<br />
						<small>
						    офис: <?= $invoice['oname'] ?><br />
						    кем выдан счёт: <?= $invoice['uname'] ?><br/>
						    (аннулирован: <?= date('d.m.y', strtotime($invoice['dvisible'])) ?>, кем: <?= $invoice['uvisible'] ?>)
						</small>
						</li>
					    <?php } ?>
				    <?php } ?>
			</ul>
	    </div>
	</div>
<?php
    }
}
?>
<p class="text-right">общая сумма счетов: <strong><?= $invoicesum ?></strong> р.</p>
<p class="text-right">отработанных счетов: <strong><?= $invoicedone ?></strong> из <strong><?= $invoiceall ?></strong></p>
