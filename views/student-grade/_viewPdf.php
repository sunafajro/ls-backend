<?php

/**
 * @var yii\web\View $this
 * @var array $attestation
 * @var array $contentTypes
 * @var array $exams
 */

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
$contents = [];
if ($attestation['contents']) {
  $json = json_decode($attestation['contents']);
  foreach($json ?? [] as $key => $value) {
    $contents[] = ($contentTypes[$key] ?? $key) . ': ' . $value;
  }
}
?>
<div class="body">
<div class="outer-block">
      <div class="header-block">
        <div class="logo-block">
          <?= Html::img(Url::to('./uploads/yazyk_uspekha_logo_1.png'), ['class' => 'logo']) ?>
        </div>
        <div class="title-block">
          <div>
            Негосударственное образовательное учреждение дополнительного и
            дополнительного профессионального образования
          </div>
          <div>
            Школа иностранных языков "Язык для успеха"
          </div>
        </div>
        <div style="clear: both"></div>
      </div>
      <div class="reginfo">
          Дата: <?= date('d.m.Y', strtotime($attestation['date'])) ?><br />
          Регистрационный номер: <?= date('ymd', strtotime($attestation['date'])) . '-' . $attestation['id'] ?><br />
          г. Чебоксары<br />
      </div>
      <div class="text-description-block">
          Настоящим удостоверяется, что
      </div>
      <div class="text-result-block">
          <?= $attestation['studentName'] ?>
      </div>
      <div class="text-description-block">
          сдал
      </div>
      <div class="text-result-block">
          <?= $exams[$attestation['description']] ?? $attestation['description'] ?>
      </div>
      <div class="text-description-block">
          с результатом
      </div>
      <div class="text-result-block">
          <?= implode(', ', $contents); ?>
      </div>
      <div class="text-description-block">
          итог/уровень
      </div>
      <div class="text-result-block">
          <?= $attestation['score'] ?>
      </div>
      <div class="sign-block">
        <div class="left-sign-block">
            Директор   
        </div>
        <div class="right-sign-block">
            Филиппова А.К.
        </div>
        <div style="clear: both"></div>
      </div>
      <div class="licence">
          Лицензия Министерства образования ЧР № 799 от 27.02.2014 г. серия 21ЛО1 № 0000152
      </div>
    </div>
</div>
