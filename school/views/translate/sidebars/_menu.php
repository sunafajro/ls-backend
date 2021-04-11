<?php
/**
 * @var string $activeItem
 */

use school\models\AccessRule;
use yii\helpers\Html; ?>
<div class="form-group">
    <div class="dropdown">
        <button id="dropdownMenu-1" type="button" class="btn btn-default dropdown-toggle btn-block" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Разделы
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenu-1">
            <?php if (AccessRule::checkAccess('translate_translations')) { ?>
                <li class="<?= $activeItem === 'translations' ? 'active' : '' ?>">
                    <?= Html::a(Yii::t('app','Translations'), ['translate/translations']) ?>
                </li>
            <?php } ?>
            <?php if (AccessRule::checkAccess('translate_translators')) { ?>
                <li class="<?= $activeItem === 'translators' ? 'active' : '' ?>">
                    <?= Html::a(Yii::t('app','Translators'), ['translate/translators']) ?>
                </li>
            <?php } ?>
            <?php if (AccessRule::checkAccess('translate_clients')) { ?>
                <li class="<?= $activeItem === 'clients' ? 'active' : '' ?>">
                    <?= Html::a(Yii::t('app','Clients'), ['translate/clients']); ?>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>
<div class="form-group">
    <div class="dropdown">
        <button id="dropdownMenu-2" type="button" class="btn btn-default dropdown-toggle btn-block" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Справочники
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenu-2">
            <?php if (AccessRule::checkAccess('translate_languages')) { ?>
                <li class="<?= $activeItem === 'languages' ? 'active' : '' ?>">
                    <?= Html::a(Yii::t('app','Languages'), ['translate/languages']) ?>
                </li>
            <?php } ?>
            <?php if (AccessRule::checkAccess('translate_norms')) { ?>
                <li class="<?= $activeItem === 'payNorms' ? 'active' : '' ?>">
                    <?= Html::a(Yii::t('app','Pay norms'), ['translate/norms']) ?>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>
