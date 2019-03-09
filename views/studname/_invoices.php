<?php
	use yii\helpers\Html;
?>

<?php
	$invtype = [1 => 'In work', 2 => 'Done', 3 => 'Canceled'];
	$invoicesum = 0;
	$invoicedone = 0;
	$invoiceall = 0;
	// запускаем трехшаговый цикл для распечатки счетов по типу
	for ($i = 1; $i <= 3; $i++) {
		// проверяем что есть счета для распечатки
		if ($invcount[$i] > 0) {
			echo "<div class='panel panel-default'>";
			echo "<div class='panel-heading' role='tab' id='collapseInvoiceGroupHeading".$i."'>";
			echo "<h4 id='-collapsible-list-group-' class='panel-title'>";
			if ($i == 1) {
			    echo "<button type='button' class='btn btn-xs btn-default' data-container='body' data-toggle='popover' data-placement='top' data-content='Менеджеры, не забывайте отслеживать отработанные счета и вовремя ставить отметку \"отработанный\"'><span class='glyphicon glyphicon-info-sign'></span></button> ";
			}
			if($i == 1) {
				echo "<a data-toggle='collapse' href='#collapseInvoiceGroup".$i."' aria-expanded='true' aria-controls='collapseInvoiceGroup".$i."'>";
			} else {
				echo "<a class='collapsed' data-toggle='collapse' href='#collapseInvoiceGroup".$i."' aria-expanded='false' aria-controls='collapseInvoiceGroup".$i."'>";
			}
			// выводим название типа счета и их количество
			echo Yii::t('app',$invtype[$i])." (".$invcount[$i].")</a>";
			echo "</h4></div>";
			if($i==1){
				echo "<div aria-expanded='true' id='collapseInvoiceGroup".$i."' class='panel-collapse collapse in' role='tabpanel' aria-labelledby='collapseInvoiceGroupHeading".$i."'>";
			} else {
				echo "<div style='height: 0px;' aria-expanded='false' id='collapseInvoiceGroup".$i."' class='panel-collapse collapse' role='tabpanel' aria-labelledby='collapseInvoiceGroupHeading".$i."'>";
			}
			echo "<ul class='list-group'>";
				foreach ($invoices as $invoice) {
					// распечатываем счета - В работе
					if ($i == 1 && $invoice['idone'] == 0 && $invoice['ivisible'] == 1) {
						echo "<li class='list-group-item list-group-item-warning'>";
                                                echo $invoice['remain'] ? ' (остаточный) ' : '';
						echo $invoice['sname']." (услуга#".$invoice['sid']." от ".date('d.m.y', strtotime($invoice['idate'])).") ".$invoice['inum']." зан. на сумму ".$invoice['ivalue']." р.<br />";
						// если к счету была применена ссылка, выводим информацию о ней
						if($invoice['perm_sale']||$invoice['rub_sale']||$invoice['proc_sale']) {
							echo "<small><em>Учтены скидки: ";
							$sales = "";
							if($invoice['perm_sale']) {
								$sales .= $invoice['perm_sale'];
							}
							if($invoice['rub_sale']) {
								$sales .= ($invoice['perm_sale'] ? ", " : "");
								$sales .= $invoice['rub_sale'];
							}
							if($invoice['proc_sale']) {
								$sales .= (($invoice['perm_sale']||$invoice['rub_sale']) ? ", " : "");
								$sales .= $invoice['proc_sale'];
							}
							echo $sales;
							unset($sales);
							echo "</em></small><br />";
						}
						echo Html::a('Аннулировать', ['invoice/disable', 'id' => $invoice['iid']]);
						echo $invoice['remain'] ? ' | ' . Html::a("Установить как Обычный",['invoice/unremain', 'id'=>$invoice['iid']]) : ' | ' . Html::a("Установить как Остаточный",['invoice/remain', 'id'=>$invoice['iid']]);
                        echo ' | ' . Html::a((int)$invoice['oid'] !== 6 ? Yii::t('app', 'Make corporative') : Yii::t('app', 'Make normal'), ['invoice/corp', 'id' => $invoice['iid']]);
						echo ' | ' . Html::a('Отработан', ['invoice/done', 'id' => $invoice['iid']]);
						echo "<br /><small>";
						echo "офис: ".$invoice['oname']."<br />";
						echo "кем выдан счёт: ".$invoice['uname'];
						echo "</small></li>";
						$invoicesum+=$invoice['ivalue'];
						$invoiceall+=1;
					}
					// распечатываем счета - Отработанные
					if ($i == 2 && $invoice['idone'] == 1 && $invoice['ivisible'] == 1) {
						echo "<li class='list-group-item list-group-item-success'>";
						echo $invoice['sname']." (услуга#".$invoice['sid']." от ".date('d.m.y', strtotime($invoice['idate'])).") ".$invoice['inum']." зан. на сумму ".$invoice['ivalue']." р.<br />";
						// если к счету была применена ссылка, выводим информацию о ней
						if($invoice['perm_sale']||$invoice['rub_sale']||$invoice['proc_sale']) {
							echo "<small><em>Учтены скидки: ";
							$sales = "";
							if($invoice['perm_sale']) { 
								$sales .= $invoice['perm_sale']; 
							}
							if($invoice['rub_sale']) { 
								$sales .= ($invoice['perm_sale'] ? ", " : "");
								$sales .= $invoice['rub_sale']; 
							}
							if($invoice['proc_sale']) {
								$sales .= (($invoice['perm_sale']||$invoice['rub_sale']) ? ", " : ""); 
								$sales .= $invoice['proc_sale']; 
							}
							echo $sales;
							unset($sales);
							echo "</em></small><br />";
						}
						echo Html::a("Вернуть в работу",['invoice/undone','id'=>$invoice['iid']]);
						echo "<br /><small>";
						echo "офис: ".$invoice['oname']."<br />";
						echo "кем выдан счёт: ".$invoice['uname']."<br/>";
						echo "(отработан: ".date('d.m.y', strtotime($invoice['ddone'])).", кем: ".$invoice['udone'].")";
						echo "</small></li>";
						$invoicesum+=$invoice['ivalue'];
						$invoicedone+=1;
						$invoiceall+=1;
					}
					// распечатываем счета - Аннулированные
					if ($i == 3 && $invoice['ivisible'] == 0) {
						echo "<li class='list-group-item list-group-item-danger'>";
						echo $invoice['sname']." (услуга#".$invoice['sid']." от ".date('d.m.y', strtotime($invoice['idate'])).") ".$invoice['inum']." зан. на сумму ".$invoice['ivalue']." р.<br />";
						// если к счету была применена ссылка, выводим информацию о ней
						if($invoice['perm_sale']||$invoice['rub_sale']||$invoice['proc_sale']) {
							echo "<small><em>Учтены скидки: ";
							$sales = "";
							if($invoice['perm_sale']) { 
								$sales .= $invoice['perm_sale']; 
							}
							if($invoice['rub_sale']) { 
								$sales .= ($invoice['perm_sale'] ? ", " : "");
								$sales .= $invoice['rub_sale']; 
							}
							if($invoice['proc_sale']) {
								$sales .= (($invoice['perm_sale']||$invoice['rub_sale']) ? ", " : ""); 
								$sales .= $invoice['proc_sale']; 
							}
							echo $sales;
							unset($sales);
							echo "</em></small><br />";
						}
						echo Html::a("Снова использовать",['invoice/enable','id'=>$invoice['iid']]);
						if(Yii::$app->session->get('user.ustatus')==3){
							echo " | ".Html::a("Удалить",['invoice/delete','id'=>$invoice['iid']]);
						}
						echo "<br /><small>";
						echo "офис: ".$invoice['oname']."<br />";
						echo "кем выдан счёт: ".$invoice['uname']."<br/>";
						echo "(аннулирован: ".date('d.m.y', strtotime($invoice['dvisible'])).", кем: ".$invoice['uvisible'].")";
						echo "</small></li>";
					}
				} ?>
			</ul>
	    </div>
	</div>
	<?php } ?>
	<?php } ?>
	<p class="text-right">общая сумма счетов: <strong><?= $invoicesum ?></strong> р.</p>
	<p class="text-right">отработанных счетов: <strong><?= $invoicedone ?></strong> из <strong><?= $invoiceall ?></strong></p>
