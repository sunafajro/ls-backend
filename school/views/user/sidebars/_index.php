<?php

/**
 * @var View  $this
 */

use common\components\helpers\IconHelper;
use school\models\AccessRule;
use yii\helpers\Html;
use yii\web\View;

if (AccessRule::checkAccess('user_create')) {
?>
    <h4><?= Yii::t('app', 'Actions') ?>:</h4>
    <div class="form-group">
        <?= Html::a(
            IconHelper::icon('plus', Yii::t('app', 'Add')),
            ['user/create'],
            ['class' => 'btn btn-success btn-sm btn-block']
        ) ?>
    </div>
<?php } ?>
<ul>
    <li>Для просмотра удаленных пользователей, поменйте фильтр стратуса "Отключенные".</li>
</ul>
