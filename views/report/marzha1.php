<table class="table table-bordered">
    <thead>
        <th>Преподаватель</th>
        <th>Кол-во зан.</th>
        <th>Доход (без скидок)</th>
        <th>Оплата</th>
        <th>Маржа</th>
        <th>Маржа к доходу</th>
        <th>Маржа за занятие</th>
    </thead>
    <tbody>
        <?php foreach($teachers as $t) { ?>
        <tr>
            <td><?php echo $t['tname']; ?></td>
            <td class="text-center"><?php echo $t['cnt']; ?></td>
            <td class="text-center"><?php echo number_format($t['income'], 2, ',', ' '); ?></td>
            <td class="text-center"><?php echo number_format($t['expense'], 2, ',', ' '); ?></td>
            <td class="text-center"><?php echo number_format(($t['income'] - $t['expense']), 2, ',', ' '); ?></td>
            <td class="text-center">
            <?php if($t['income'] > 0) {
                    echo round(100 * (($t['income'] - $t['expense']) / $t['income']));
                } else {
                    echo 0.00;    
                } ?> %</td>
            <td class="text-center"><?php echo number_format((($t['income'] - $t['expense'])/$t['cnt']), 2, ',', ' '); ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>