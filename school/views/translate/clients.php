<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\widgets\Breadcrumbs;
    $this->title = 'Система учета :: '.Yii::t('app','Clients');
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
    $this->params['breadcrumbs'][] = Yii::t('app','Clients');
?>

<div class="row row-offcanvas row-offcanvas-left translation-clients">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
        <?= $userInfoBlock ?>
        <h4><?= Yii::t('app', 'Actions') ?>:</h4>
        <div class="form-group">
            <?= Html::a('<span class="fa fa-plus" aria-hidden="true"></span> ' . Yii::t('app', 'Add'), ['translationclient/create'], ['class' => 'btn btn-success btn-sm btn-block']) ?>
        </div>
        <div class="form-group">
            <div class="dropdown">
                <button id="dropdownMenu-1" type="button" class="btn btn-default dropdown-toggle btn-block" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Разделы
                <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu-1">
                    <li><?php echo Html::a(Yii::t('app','Translations'), ['translate/translations']); ?></li>
                    <li><?php echo Html::a(Yii::t('app','Translators'), ['translate/translators']); ?></li>
                    <li class="active"><?php echo Html::a(Yii::t('app','Clients'), ['translate/clients']); ?></li>
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
                    'action' => ['translate/clients'],
                ]);
            ?>
        <div class="form-group">
            <input type="text" class="form-control input-sm" placeholder="Найти по имени..." name="TSS"<?= ($url_params['TSS'] != NULL) ? ' value="' . $url_params['TSS'] . '"' : '' ?>>
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
        
        <table class="table table-stripped table-bordered table-hover table-condensed small">
        <thead>
            <tr>
                <th>#</th>
                <th>Наименование орг.</th>
                <th>Адрес</th>
                <th>Контактное лицо</th>
                <th>Телефон</th>
                <th>Э.почта</th>
                <th>Комментарии</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; ?>
            <?php foreach($clients as $c): ?>
            <tr>
                <td><?= $i ?></td>
                <td><?= $c['name'] ?></td>
                <td><?= $c['address'] ?></td>
                <td><?= $c['contact'] ?></td>
                <td><?= $c['phone'] ?></td>
                <td><?= $c['email'] ?></td>
                <td><?= $c['description'] ?></td>
                <td class="text-center">
                <?= Html::a('<span class="fa fa-pencil" aria-hidden="true"></span>', ['translationclient/update', 'id'=>$c['id']], ['title'=>Yii::t('app','Edit')]) ?>
                <?= Html::a('<span class="fa fa-trash" aria-hidden="true"></span>', ['translationclient/disable', 'id'=>$c['id']], ['title'=>Yii::t('app','Delete')]) ?>
                </td>
            </tr>
            <?php $i++; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>