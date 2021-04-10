<?php
/**
 * @var View $this
 * @var array $menuLinks
 */

use common\components\helpers\IconHelper;
use school\models\AccessRule;
use yii\helpers\Html;
use yii\web\View;
?>
<?= $this->render('_menu', ['menuLinks' => $menuLinks]) ?>
<h4><?= Yii::t('app', 'Actions') ?></h4>
<?php
    if (AccessRule::checkAccess('education-level_create')) {
        echo Html::a(IconHelper::icon('plus') . ' ' . Yii::t('app', 'Add'), ['education-level/create'], ['class' => 'btn btn-success btn-sm btn-block']);
    }
?>
