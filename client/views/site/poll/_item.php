<?php
/**
 * @var integer $questionId
 * @var array $item
 */

use yii\helpers\Html;

?>
<div>
    <?= Html::checkbox("PollResponse[{$questionId}][{$item['id']}]") ?> <?= $item['title'] ?>
</div>
