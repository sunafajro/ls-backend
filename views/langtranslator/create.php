<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Langtranslator */

$this->title = 'Система учета :: ' . Yii::t('app', 'Add language');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translations'), 'url' => ['translate/translations']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Translators'), 'url' => ['translate/translators']];
$this->params['breadcrumbs'][] = $translator->name;
$this->params['breadcrumbs'][] = Yii::t('app', 'Add language');
?>
<div class="row row-offcanvas row-offcanvas-left translator-language-create">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= $userInfoBlock ?>
		<ul>
			<li>Выберите язык из выпадающего списка и нажмите кнопку Добавить.</li>
			<li>Можно убрать язык при помощи иконки рядом с названием языка.</li>
		</ul>
	</div>
	<div id="content" class="col-sm-6">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
		</p>
        <?php foreach($trlangs as $tl): ?>
            <p><strong><?= $tl['lname'] ?></strong> <?= Html::a('<span class="fa fa-trash" aria-hidden="true"></span>', ['langtranslator/delete', 'id'=>$tl['id']], ['title'=>Yii::t('app','Delete')]) ?><br />
            <span class="text-muted">кем добавлен:</span> <?= $tl['user'] ?><br />
            <span class="text-muted">когда добавлен:</span> <?= date('d.m.y', strtotime($tl['date'])) ?><p>
        <?php endforeach; ?>
        <hr />

    <?= $this->render('_form', [
            'model' => $model,
            'language'=>$language,
        ]) ?>
    </div>
</div>
