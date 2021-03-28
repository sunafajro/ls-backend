<?php
/**
 * @var View $this
 * @var array $formReceiptData
 */

use yii\helpers\Html;
use yii\web\View;
?>
<h4>Основные параметры:</h4>
<?php foreach($formReceiptData as $row) { ?>
    <div class="form-group">
        <b><?= $row['title'] ?></b>
        <?= Html::input('text', '', $row['value'], ['class' => 'form-control', 'disabled' => true]) ?>
    </div>
<?php } ?>
