<?php

/**
 * @var yii\web\View $this
 * @var string       $email
 * @var array        $payments
 * @var array        $years
 */

use Yii;
use app\models\Notification;
use yii\helpers\Html;
?>

<?php $totalpayment = 0; ?>
<?php foreach($years as $key => $y) { ?>
<div class="panel panel-default">
    <div class="panel-heading" role="tab" id="collapsePaymentGroupHeading<?= $key ?>">
        <h4 id="-collapsible-list-group-" class="panel-title">
            <a class="collapsed" data-toggle="collapse" href="#collapsePaymentGroup<?= $key ?>" aria-expanded="false" aria-controls="collapsePaymentGroup<?= $key ?>">
				<?= Yii::t('app', date('F', strtotime($y))) . ' ' . date('Y', strtotime($y)) ?>
		    </a>
            <a class="anchorjs-link" href="#-collapsible-list-group-">
				<span class="anchorjs-icon"></span>
			</a>
		</h4>
    </div>
    <div style="height: 0px" aria-expanded="false" id="collapsePaymentGroup<?= $key ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="collapsePaymentGroupHeading<?= $key ?>">
        <ul class="list-group">
		<?php foreach($payments as $payment) { ?>
            <?php if (substr($payment['pdate'], 0, 7) === substr($y, 0, 7)) { ?>
                <li class="list-group-item<?= (int)$payment['visible'] === 0 ? ' list-group-item-danger' : '' ?>">
                    <small>
						<span class="inblocktext">
							<?=
							   (int)$payment['visible'] === 1 && $payment['notificationId'] ?
									'<i class="fa fa-envelope ' . Notification::getTextColorClassByStatus($payment['notificationStatus']) . '" aria-hidden="true" title="'. Yii::t('app', Notification::getStatusLabel($payment['notificationStatus'])) . '"></i>' :
									''
							?>
							<?= $payment['remain'] ? ' (остаточная) ' : ''; ?>
							<strong>
								оплата от <span class="text-danger"><?= date('d.m.y', strtotime($payment['pdate'])) ?></span> на сумму <span class="text-danger"><?= $payment['pvalue'] ?></span> р. <?= $payment['receipt'] ? ' (' . $payment['receipt'] . ') ' : '' ?>
							</strong>
						</span>
						<br />
					<?php
					    $actions = [];
					    if ((int)$payment['visible'] === 1) {
							if ($email) {
								if ($payment['notificationId']) {
									if ($payment['notificationStatus'] !== Notification::STATUS_QUEUE) {
									    $actions[] = Html::a(Yii::t('app', 'Resend'), ['notification/resend', 'id' => $payment['notificationId']], ['data' => ['method' => 'post']]);
									}
								} else {
									$actions[] = Html::a(Yii::t('app', 'Send'), ['notification/create', 'type' => Notification::TYPE_PAYMENT, 'id' => $payment['pid']], ['data' => ['method' => 'post']]);
								}
						    }
						    $actions[] = Html::a('Аннулировать', ['moneystud/disable', 'id' => $payment['pid']]);
							$actions[] = $payment['remain'] ?
							    Html::a(Yii::t('app', 'Make normal'), ['moneystud/unremain', 'id'=> $payment['pid']]) :
							    Html::a(Yii::t('app', 'Make remain'), ['moneystud/remain', 'id' => $payment['pid']]);
				        } else {
					        $actions[] = Html::a('Снова использовать', ['moneystud/enable', 'id' => $payment['pid']]);
				        }
				        if ((int)Yii::$app->session->get('user.ustatus') === 3) {
						    $actions[] = Html::a('Удалить', ['moneystud/delete', 'id' => $payment['pid']]);
					    }
						echo implode(' | ', $actions);
					?>
					<br />
					<span class="inblocktext">офис: <strong><?php echo $payment['oname']; ?></strong></span><br />
					<span class="inblocktext">кем выдан счёт: <strong><?php echo $payment['uname']; ?></strong></span>
                    <?php if ($payment['edit_date'] != '0000-00-00') { ?>
						<br />
						<span class="inblocktext">
						<?php if ((int)$payment['visible'] === 1) { ?>
							(восстановлена: <?= date('d.m.y', strtotime($payment['edit_date'])) ?>, кем: <?= $payment['editor'] ?>)
					    <?php } else { ?>
							(аннулирована: <?= date('d.m.y', strtotime($payment['edit_date'])) ?>, кем: <?= $payment['editor'] ?>)
						<?php } ?>
						</span>
					<?php } ?>                         
					</small>
				</li>
				<?php
				    if ((int)$payment['visible'] === 1) {
				        $totalpayment = $totalpayment + $payment['pvalue'];
					}
				?>
			<?php } ?>
		<?php } ?>
		</ul>
	</div>
</div>
<?php } ?>
<p class="text-right">общая сумма оплат: <strong><?= $totalpayment ?></strong> р.</p>