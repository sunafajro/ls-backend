<?php

use Yii;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View  $this
 * @var array $commissions
 * @var array $years
 */
?>
<?php $totalCommission = 0; ?>
<?php foreach ($years as $key => $y) { ?>
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
		<?php foreach ($commissions as $commission) { ?>
            <?php if (substr($commission['date'], 0, 7) === substr($y, 0, 7)) { ?>
                <li class="list-group-item small">
					<b>
						комиссия от <span class="text-danger"><?= date('d.m.y', strtotime($commission['date'])) ?></span>, <span class="text-danger"><?= $commission['percent'] ?></span>% от долга <span class="text-danger"><?= abs($commission['debt']) ?></span> р., на сумму <span class="text-danger"><?= $commission['value'] ?></span> р.
					</b>
					<br />
                    <?= $commission['comment'] ? '(' . $commission['comment'] . ')' . Html::tag('br') : ''; ?>
					<?= Html::a('Аннулировать', ['student-commission/delete', 'id' => $commission['id']]) ?>
					<br />
					<span class="inblocktext">офис: <b><?php echo $commission['office']; ?></b></span><br />
					<span class="inblocktext">кем выставлена комиссия: <b><?php echo $commission['user']; ?></b></span>                       
				</li>
				<?php $totalCommission = $totalCommission + $commission['value']; ?>
			<?php } ?>
		<?php } ?>
		</ul>
	</div>
</div>
<?php } ?>
<p class="text-right">общая сумма комиссий: <b><?= $totalCommission ?></b> р.</p>