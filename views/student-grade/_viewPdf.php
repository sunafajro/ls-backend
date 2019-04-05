<?php

/**
 * @var yii\web\View $this
 * @var array $attestation
 */

use Yii;
?>
<p>
    <b><?= Yii::t('app', 'Date') ?>:</b> <?= date('d.m.Y', strtotime($attestation['date'])) ?>
</p>
<p>
    <b><?= Yii::t('app', 'Description') ?>:</b> <?= $attestation['description'] ?>
</p>
<p>
    <b><?= Yii::t('app', 'Score') ?>:</b> <?= $attestation['score'] ?>
</p>
<p>
    <b><?= Yii::t('app', 'Student') ?>:</b> <?= $attestation['studentName'] ?>
</p>