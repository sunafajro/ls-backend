<?php

/**
 * @var yii\web\View $this
 * @var array        $pager
 * @var string       $url
 */

use Yii;
use yii\helpers\Html;

$end = $pager['offset'] + $pager['limit'];
$start = $pager['total'] ? $pager['offset'] + 1 : $pager['offset'];

$previous = $url;
$previous['offset'] = $pager['offset'] - $pager['limit'];

$next = $url;
$next['offset'] = $end;
?>
<div class="row">
    <div class="col-xs-12 col-sm-4">
        <?php if ($pager['offset'] > 0) { ?>
            <?= Html::a(
                Yii::t('app', 'Previous'),
                $previous,
                ['class' => 'btn btn-default btn-sm'])
            ?>
        <?php } ?>
    </div>
    <div class="col-xs-12 col-sm-4 text-center">
        <p style="margin-top: 1rem; margin-bottom: 0.5rem">
            Показано <?= $start ?> - <?= $end >= $pager['total'] ? $pager['total'] : $end ?> из <?= $pager['total'] ?>
        </p>
    </div>
    <div class="col-xs-12 col-sm-4 text-right">
        <?php if ($end < $pager['total']) { ?>
            <?= Html::a(
                Yii::t('app', 'Next'),
                $next,
                ['class' => 'btn btn-default btn-sm']
            ) ?>
        <?php } ?>
    </div>
</div>