<?php
/**
 * @var string $activeItem
 */
use yii\helpers\Html; ?>
<div class="form-group">
    <div class="dropdown">
        <button id="dropdownMenu-1" type="button" class="btn btn-default dropdown-toggle btn-block" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Разделы
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenu-1">
            <li class="<?= $activeItem === 'translations' ? 'active' : '' ?>"><?= Html::a(Yii::t('app','Translations'), ['translate/translations']) ?></li>
            <li class="<?= $activeItem === 'translators' ? 'active' : '' ?>"><?= Html::a(Yii::t('app','Translators'), ['translate/translators']) ?></li>
            <li class="<?= $activeItem === 'clients' ? 'active' : '' ?>"><?= Html::a(Yii::t('app','Clients'), ['translate/clients']); ?></li>
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
            <li class="<?= $activeItem === 'languages' ? 'active' : '' ?>"><?= Html::a(Yii::t('app','Languages'), ['translate/languages']) ?></li>
            <li class="<?= $activeItem === 'payNorms' ? 'active' : '' ?>"><?= Html::a(Yii::t('app','Pay norms'), ['translate/norms']) ?></li>
        </ul>
    </div>
</div>
