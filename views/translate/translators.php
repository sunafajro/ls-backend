<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Система учета :: '.Yii::t('app','Translators');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
$this->params['breadcrumbs'][] = Yii::t('app','Translators');

?>

<div class="row row-offcanvas row-offcanvas-left translator-index">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= $userInfoBlock ?>
        <h4><?= Yii::t('app', 'Actions') ?>:</h4>
        <div class="form-group">
            <?= Html::a('<span class="fa fa-plus" aria-hidden="true"></span> ' . Yii::t('app', 'Add'), ['translator/create'], ['class' => 'btn btn-success btn-sm btn-block']) ?>
        </div>
        <div class="form-group">
            <div class="dropdown">
                <button id="dropdownMenu-1" type="button" class="btn btn-default dropdown-toggle btn-block" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Разделы
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu-1">
                    <li><?php echo Html::a(Yii::t('app','Translations'), ['translate/translations']); ?></li>
                    <li class="active"><?php echo Html::a(Yii::t('app','Translators'), ['translate/translators']); ?></li>
                    <li><?php echo Html::a(Yii::t('app','Clients'), ['translate/clients']); ?></li>
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
                    <li><?php echo Html::a(Yii::t('app','Languages'), ['translate/languages']); ?></li>
                    <li><?php echo Html::a(Yii::t('app','Pay norms'), ['translate/norms']); ?></li>
                </ul>
            </div>
        </div>
        <h4><?= Yii::t('app', 'Filters') ?>:</h4>
        <?php
            $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['translate/translators'],
            ]);
        ?>
        <div class="form-group">
            <input type="text" class="form-control input-sm" placeholder="Найти по имени..." name="TSS"<?= ($url_params['TSS'] != NULL) ? ' value="' . $url_params['TSS'] . '"' : '' ?>>
        </div>
        <div class="form-group">
            <select class='form-control input-sm' name='LANG'>";
		        <option value='all'><?= Yii::t('app', '-all languages-') ?></option>";
		    	<?php // распечатываем список лет в селект
		        foreach($languages as $key => $value){ ?>
		            <option value="<?php echo $key; ?>" <?php echo ($key==$url_params['LANG']) ? ' selected' : ''; ?>><?php echo $value; ?></option>
		        <?php } ?>
	        </select>
	    </div>
        <div class="form-group">
            <select name="NOTAR" class="form-control input-sm">
                <option value="all">-нот.завер.-</option>
                <option value="1"<?= ($url_params['NOTAR'] == '1') ? ' selected' : '' ?>>Да</option>
                <option value="0"<?= ($url_params['NOTAR'] == '0') ? ' selected' : '' ?>>Нет</option>
            </select>
        </div>
        <div class="form-group">
            <?= Html::submitButton('<span class="fa fa-filter" aria-hidden="true"></span> ' . Yii::t('app', 'Apply'), ['class' => 'btn btn-info btn-sm btn-block']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div id="content" class="col-sm-10">
        <p class="pull-left visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>

        <?php if(Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger" role="alert">
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
        <?php endif; ?>
   
        <?php if(Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success" role="alert">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
        <?php endif; ?>
        <table class="table table-stripped table-bordered table-hover table-condensed small">
            <thead>
                <tr>
                    <th>#</th>
                    <th>ФИО</th>
                    <th>Язык</th>
                    <th>Телефон</th>
                    <th>Э.почта</th>
                    <th>Нот. заверение</th>
                    <th>Ссылка</th>
                    <th>Скайп</th>
                    <th>Комментарии</th>
                    <?php if(Yii::$app->session->get('user.ustatus') ==3 || Yii::$app->session->get('user.ustatus') == 9): ?>
                        <th>Действия</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php $i = 1; ?>
            <?php foreach($translators as $t): ?>
                <tr>
                    <td><?= $i ?></td>
                    <td><?= $t['name'] ?></td>
                    <td>
                    <?php foreach($translator_languages as $l): ?>
                        <?php if($l['tid']==$t['id']): ?>
                            <?= $l['lname'] ?><br/>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </td>
                    <td><?= $t['phone'] ?></td>
                    <td><?= $t['email'] ?></td>
                    <td class='text-center'><?= ($t['notarial'] == 1) ? '<span class="fa fa-check" aria-hidden="true"></span>' : '' ?></td>
                    <td class="text-center">
                    <?php if($t['url']): ?>
                        <?= Html::a('', 'http://'.$t['url'], ['class'=>'glyphicon glyphicon-new-window', 'target'=>'_blank']) ?>
                    <?php endif; ?>
                    </td>
                    <td><?= $t['skype'] ?></td>
                    <td><?= $t['description'] ?></td>
                    <?php if(Yii::$app->session->get('user.ustatus') == 3 || Yii::$app->session->get('user.ustatus') == 9): ?>
                        <td class="text-center">
                        <?= Html::a('<span class="fa fa-language" aria-hidden="true"></span>', ['langtranslator/create', 'tid'=>$t['id']], ['title'=>Yii::t('app','Add')]) ?>
                        <?= Html::a('<span class="fa fa-pencil" aria-hidden="true"></span>', ['translator/update', 'id'=>$t['id']], ['title'=>Yii::t('app','Edit')]) ?>
                        <?= Html::a('<span class="fa fa-trash" aria-hidden="true"></span>', ['translator/disable', 'id'=>$t['id']], ['title'=>Yii::t('app','Delete')]) ?>
                        </td>
                    <?php endif; ?>
                </tr>
                <?php $i++; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>