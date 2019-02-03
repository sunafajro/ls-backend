<?php
	use yii\helpers\Html;
?>

<?php $totalpayment = 0; ?>
<?php foreach($years as $key => $y) : ?>
<div class="panel panel-default">
    <div class="panel-heading" role="tab" id="collapsePaymentGroupHeading<?= $key ?>">
        <h4 id="-collapsible-list-group-" class="panel-title">
        <a class="collapsed" data-toggle="collapse" href="#collapsePaymentGroup<?= $key ?>" aria-expanded="false" aria-controls="collapsePaymentGroup<?= $key ?>">
        <?= Yii::t('app', date('F',strtotime($y))) . ' ' . date('Y', strtotime($y)) ?></a>
        <a class="anchorjs-link" href="#-collapsible-list-group-"><span class="anchorjs-icon"></span></a></h4>
    </div>
    <div style="height: 0px" aria-expanded="false" id="collapsePaymentGroup<?php echo $key ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="collapsePaymentGroupHeading<?= $key ?>">
        <ul class="list-group">
        <?php foreach($payments as $payment) : ?>
            <?php if(substr($payment['pdate'], 0, 7) == substr($y, 0, 7)) : ?>
                <li class="list-group-item<?= ($payment['visible'] == 0) ? ' list-group-item-danger' : '' ?>">
                    <small><span class="inblocktext"><?php echo $payment['remain'] ? ' (остаточная) ' : ''; ?><strong>оплата от <span class="text-danger"><?= date('d.m.y', strtotime($payment['pdate'])) ?></span> на сумму <span class="text-danger"><?= $payment['pvalue'] ?></span> р. <?= $payment['receipt'] ? ' (' . $payment['receipt'] . ') ' : '' ?></strong></span><br />
				    <?php if($payment['visible'] == 1) {
					    echo Html::a('Аннулировать',['moneystud/disable', 'id'=>$payment['pid']]);
				    } else {
					    echo Html::a('Снова использовать',['moneystud/enable', 'id'=>$payment['pid']]);
				    }
				    echo $payment['remain'] ? ' | ' . Html::a("Установить как Обычную",['moneystud/unremain', 'id'=> $payment['pid']]) : ' | ' . Html::a("Установить как Остаточный",['moneystud/remain', 'id'=>$payment['pid']]);
				    if(Yii::$app->session->get('user.ustatus')==3){
						echo ' | ' . Html::a('Удалить',['moneystud/delete', 'id'=> $payment['pid']]);
					} ?>
					<br />
					<span class="inblocktext">офис: <strong><?php echo $payment['oname']; ?></strong></span><br />
					<span class="inblocktext">кем выдан счёт: <strong><?php echo $payment['uname']; ?></strong></span>
                    <?php if ($payment['edit_date'] != '0000-00-00') : ?>
						<br />
						<span class="inblocktext">
						<?php if ($payment['visible'] == 1) : ?>
							(восстановлена: <?= date('d.m.y', strtotime($payment['edit_date'])) ?>, кем: <?= $payment['editor'] ?>)
						<?php else : ?>
							(аннулирована: <?= date('d.m.y', strtotime($payment['edit_date'])) ?>, кем: <?= $payment['editor'] ?>)
						<?php endif; ?>
						</span>
					<?php endif; ?>                         
					</small></li>
                    <?php if($payment['visible'] == 1) {
						$totalpayment = $totalpayment + $payment['pvalue'];
					} ?>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php endforeach; ?>
<p class="text-right">общая сумма оплат: <strong><?= $totalpayment ?></strong> р.</p>