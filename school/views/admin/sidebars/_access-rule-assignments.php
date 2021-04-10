<?php
/**
 * @var View $this
 * @var array $menuLinks
 */

use common\components\helpers\IconHelper;
use yii\helpers\Html;
use yii\web\View;
?>
<?= $this->render('_menu', ['menuLinks' => $menuLinks]) ?>
<h4><?= Yii::t('app', 'Actions') ?></h4>
<?= Html::a(IconHelper::icon('plus') . ' ' . Yii::t('app', 'Add'), ['access-rule-assignment/create'], ['class' => 'btn btn-success btn-sm btn-block']) ?>
