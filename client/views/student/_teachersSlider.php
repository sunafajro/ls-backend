<?php

/**
 * @var yii\web\View $this
 * @var array $teachers
 */

use yii\helpers\Html;
?>
<div id="teachers-carousel" class="carousel slide">
    <div class="carousel-inner">
        <?php $firstItemKey = array_keys($teachers)[0]; ?>
        <?php foreach ($teachers as $key => $teacher) { ?>
            <div class="item <?= $key === $firstItemKey ? 'active' : '' ?>">
                <?php if ($teacher['photo']) { ?>
                    <?= Html::img('/uploads/user/' . $teacher['id'] . '/logo/' . $teacher['photo'], ['alt' => $teacher['name'], 'style' => 'margin: 0 auto']) ?>
                <?php } else { ?>
                    <?= Html::img('/uploads/no-photo.jpg', ['alt' => Yii::t('app', 'No photo'), 'style' => 'margin: 0 auto']) ?>
                <?php } ?>
                <div class="carousel-caption" style="bottom:-30px">
                    <b><?= $teacher['name'] ?></b><br />
                    <i><?= implode(', ', $teacher['languages']) ?></i>
                </div>
            </div>
        <?php } ?>
    </div>
    <?php if (count($teachers) > 1) { ?>
        <a class="left carousel-control" href="#teachers-carousel" role="button" data-slide="prev">
            <i class="glyphicon glyphicon-chevron-left" aria-hidden="true"></i>
        </a>
        <a class="right carousel-control" href="#teachers-carousel" role="button" data-slide="next">
            <i class="glyphicon glyphicon-chevron-right" aria-hidden="true"></i>
        </a>
    <?php } ?>
</div>
<?php
$js = <<< JS
$(function () {
  $('.carousel').carousel();
});
JS;
$this->registerJs($js);
?>
