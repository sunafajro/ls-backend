<?php

use school\models\StudentGrade;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View  $this
 * @var array $attestation
 * @var array $contentTypes
 * @var array $exams
 */
$logo = base64_encode(file_get_contents(Yii::getAlias('@uploads/yazyk_uspekha_logo_2.png')));
?>
<div class="body">
<div class="outer-block">
      <div class="header-block">
        <div class="logo-block">
          <?= Html::img("data:image/png;base64,{$logo}", ['class' => 'logo']) ?>
        </div>
        <div class="title-block">
          <div>
            Общество с ограниченной ответственностью
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
      <?php
          $fileName = 'default';
          if (in_array($attestation['description'], [StudentGrade::EXAM_TEXT_BOOK_FINAL, StudentGrade::EXAM_OLYMPIAD, StudentGrade::EXAM_DICTATION])) {
              $fileName = $attestation['description'];
          }
          echo $this->render("viewPdf/_{$fileName}", [
              'attestation' => $attestation,
              'contents'    => $attestation['contents'] ?? [],
              'exams'       => $exams,
          ]);   
      ?>
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
