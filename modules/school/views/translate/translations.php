<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\widgets\Breadcrumbs;
    $this->title = 'Система учета :: '.Yii::t('app','Translations');
    $this->params['breadcrumbs'][] = Yii::t('app','Translations');
?>

<div class="row row-offcanvas row-offcanvas-left schedule-index">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
        <?= $userInfoBlock ?>
        <h4><?= Yii::t('app', 'Actions') ?>:</h4>
        <div class="form-group">
            <?= Html::a('<span class="fa fa-plus" aria-hidden="true"></span> ' . Yii::t('app', 'Add'), ['translation/create'], ['class' => 'btn btn-success btn-sm btn-block']) ?>
            <?= Html::a(
                    Html::tag('span', '', ['class' => 'fa fa-file-text-o', 'aria-hidden' => 'true'])
                    . ' ' . Yii::t('app', 'Receipt'),
                    ['receipt/common'],
                    ['class' => 'btn btn-default btn-sm btn-block']
                ) ?>
        </div>
        <div class="form-group">
            <div class="dropdown">
                <button id="dropdownMenu-1" type="button" class="btn btn-default dropdown-toggle btn-block" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Разделы
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu-1">
                    <li class="active"><?php echo Html::a(Yii::t('app','Translations'), ['translate/translations']); ?></li>
                    <li><?php echo Html::a(Yii::t('app','Translators'), ['translate/translators']); ?></li>
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
                    'action' => ['translate/translations'],
                ]);
            ?>
        <div class="form-group">
            <input type="text" class="form-control input-sm" placeholder="Найти по наименованию..." name="TSS"<?= ($url_params['TSS'] != NULL) ? ' value="' . $url_params['TSS'] . '"' : '' ?>>
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
	        <select class='form-control input-sm' name='MONTH'>";
		        <option value='all'><?= Yii::t('app', '-all months-') ?></option>";
		    	<?php // распечатываем список месяцев в селект
		        foreach($months as $key => $value){ ?>
		            <option value="<?php echo $key; ?>" <?php echo ($key==$url_params['MONTH']) ? ' selected' : ''; ?>><?php echo $value; ?></option>
		        <?php } ?>
	        </select>
        </div>
        <div class="form-group">
            <select class='form-control input-sm' name='YEAR'>";
		        <option value='all'><?= Yii::t('app', '-all years-') ?></option>";
		    	<?php // распечатываем список лет в селект
		        foreach($years as $key => $value){ ?>
		            <option value="<?php echo $key; ?>" <?php echo ($key==$url_params['YEAR']) ? ' selected' : ''; ?>><?php echo $value; ?></option>
		        <?php } ?>
	        </select>
	    </div>
        <div class="form-group">
            <?= Html::submitButton('<span class="fa fa-filter" aria-hidden="true"></span> ' . Yii::t('app', 'Apply'), ['class' => 'btn btn-info btn-sm btn-block']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div id="content" class="col-sm-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
        ]); ?>
        <?php endif; ?>
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
        
        <?php $sum = 0; ?>

        <table class="table table-stripped table-bordered table-hover table-condensed small">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Срок обращ.</th>
                    <th>Окончание</th>
                    <th>Заказчик</th>
                    <th>Исполнитель</th>
                    <th>Напр. перевода</th>
                    <th>Наим. файла</th>
                    <th>Стоим. за 1 уч.ед.</th>
                    <th>Кол-во п.з. / у.е.</th>
                    <th>Сумма, р</th>
                    <th>Примеч.</th>
                    <th>Счет</th>
                    <?php if(Yii::$app->session->get('user.ustatus') == 3 || Yii::$app->session->get('user.ustatus') == 9): ?>
                        <th>Действ.</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php $i = 1; ?>
            <?php foreach($translations as $t): ?>
                <tr>
                    <td><?= $i ?></td>
                    <td><?= date('d.m.y', strtotime($t['tdate'])) ?></td>
                    <td><?= ($t['tenddate']!='0000-00-00' ? date('d.m.y', strtotime($t['tenddate'])) : '') ?></td>
                    <td><?= $t['client'] ?></td>
                    <td><?= $t['translator'] ?></td>
                    <td><?= mb_substr($t['from_lang'], 0, 3)."-".mb_substr($t['to_lang'],0,3) ?></td>
                    <td><?= $t['nomination'] ?></td>
                    <td><?= $t['tnorm'] ?></td>
                    <td><?= number_format($t['pscount'], 0, ',', ' ') ?><br/><?= number_format($t['aucount'], 2, ',', ' ') ?></td>
                    <td><?= number_format($t['value'], 2, ',', ' ') ?></td>
                    <td><?= $t['desc'] ?></td>
                    <td><?= $t['receipt'] ?></td>
                    <?php if(Yii::$app->session->get('user.ustatus') ==  3|| Yii::$app->session->get('user.ustatus') == 9): ?>
                    <td class="text-center">
                        <?= Html::a('<span class="fa fa-pencil" aria-hidden="true"></span>', ['translation/update', 'id'=>$t['tid']], ['title'=>Yii::t('app','Edit')]) ?>
                        <?= Html::a('<span class="fa fa-trash" aria-hidden="true"></span>', ['translation/disable', 'id'=>$t['tid']], ['title'=>Yii::t('app','Delete')]) ?>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php $i++; ?>
                <?php $sum = $sum + $t['value']; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="text-right"><strong>Итого: <?= number_format($sum, 2, ',', ' ') ?></strong></div>
        <?php $procent = 5; ?>
        <?php $proc = 0.05; ?>
        <?php if($sum>60000): ?>
            <?php $procent = 10; ?>
            <?php $proc = 0.1; ?>
        <?php endif; ?>
        <div class='text-right'><strong><?= $procent ?>%: <?=number_format($sum * $proc, 2, ',', ' ') ?></strong></div>
    </div>
</div>