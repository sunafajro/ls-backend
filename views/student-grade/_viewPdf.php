<?php

/**
 * @var yii\web\View $this
 * @var array $attestation
 */

use Yii;
?>
<div style="padding: 2rem 5rem">
    <div style="text-align: center; font-size: 18px; margin-bottom: 2rem">
        Негосударственное образовательное учреждение дополнительного и дополнительного профессионального образования
    </div>
    <div style="text-align: center; font-size: 18px; margin-bottom: 3rem">
        Школа иностранных языков "Язык для успеха"
    </div>
    <div style="margin-bottom: 3rem; font-size: 18px;">
        Дата: <?= date('d.m.Y', strtotime($attestation['date'])) ?><br />
        Регистрационный номер: <?= date('ymd', strtotime($attestation['date'])) . '-' . $attestation['id'] ?><br />
        г. Чебоксары<br />
    </div>
    <div style="margin-left: 2rem; margin-right: 2rem; margin-bottom: 1rem; font-size: 18px;">
        Настоящим удостоверяется, что
    </div>
    <div style="margin-left: 2rem; margin-right: 2rem; text-align: center; margin-bottom: 1rem; font-size: 24px; font-style: italic; border-bottom: 1px solid #999999">
        <?= $attestation['studentName'] ?>
    </div>
    <div style="margin-left: 2rem; margin-right: 2rem; margin-bottom: 1rem; font-size: 18px;">
        сдал
    </div>
    <div style="margin-left: 2rem; margin-right: 2rem; text-align: center; margin-bottom: 1rem; font-size: 24px; font-style: italic; border-bottom: 1px solid #999999">
        <?= $attestation['description'] ?>
    </div>
    <div style="margin-left: 2rem; margin-right: 2rem; margin-bottom: 1rem; font-size: 18px;">
        и получил
    </div>
    <div style="margin-left: 2rem; margin-right: 2rem; text-align: center; font-size: 24px; font-style: italic; border-bottom: 1px solid #999999">
        <?= $attestation['score'] ?>
    </div>
    <div style="position: fixed; bottom: 2rem">
        Лицензия Министерства образования ЧР № 799 от 27.02.2014 г. серия 21ЛО1 № 0000152
    </div>
</div>