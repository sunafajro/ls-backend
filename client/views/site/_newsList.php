<?php

/**
 * @var $this yii\widgets\ListView
 * @var $model
 */

use yii\helpers\Html;
?>
<div class="panel panel-success">
    <div class="panel-heading"><?= date('d.m.Y', strtotime($model['date'])) ?> :: <?= $model['title'] ?></div>
    <div class="panel-body">
        <?php if ($model['files']) { 
            $files = $model['files'];
            $addr = explode('|', $files);
            if (isset($addr[0]) && $addr[0] != "") {
                $addr = explode('|', $files);
                $ext = explode('.', $addr[0]);
                if ($ext[1] === 'jpg' || $ext[1] === 'png' || $ext[1] === 'bmp' || $ext[1] ==='gif' ) { ?>
                    <?= Html::img(
                        '@web/uploads/calc_message/' . $model['id'] . '/fls/' . $addr[0],
                        [
                            'class' => 'img-thumbnail',
                            'style' => 'margin-right: 10px; float: left'
                        ]) ?>
                    <p style="text-align: justify">
                        <?= $model['text'] ?>
                    </p>
                <?php } else { ?>
                    <p style="text-align: justify">
                        <?= $model['text'] ?>
                    </p>
                    <?= Html::a(
                        count($addr) > 1 ? $addr[1] : '<none>',
                        '@web/uploads/calc_message/' . $model['id'] . '/fls/' . $addr[0]) ?>
                <?php } ?>
            <?php } ?>
        <?php } else { ?>
            <p style="text-align: justify">
                <?= $model['text'] ?>
            </p>
        <?php } ?>
    </div>
</div>