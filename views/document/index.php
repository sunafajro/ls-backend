<?php

/**
 * @var View       $this
 * @var UploadForm $uploadForm
 * @var File[]     $fileList
 * @var string     $userInfoBlock
 */

use app\models\File;
use app\models\UploadForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use app\widgets\Alert;
use yii\widgets\Breadcrumbs;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Documents');
$this->params['breadcrumbs'][] = Yii::t('app','Documents');
$roleId = (int)Yii::$app->session->get('user.ustatus');
?>
<div class="row row-offcanvas row-offcanvas-left document-index">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
		<?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?>
        <?php if (in_array($roleId, [3, 4])) { ?>
            <h4><?= Yii::t('app', 'Actions') ?></h4>
            <?php $form = ActiveForm::begin([
                'method' => 'post',
                'action' => ['document/upload'],
                'options' => ['enctype' => 'multipart/form-data']
            ]); ?>
                <?= $form->field($uploadForm, 'file')->fileInput()->label(Yii::t('app','File')) ?>
                <div class="form-group">
                    <?= Html::submitButton(
                        Html::tag('i', ' ' . Yii::t('app','Upload'), ['class' => 'fa fa-upload', 'aria-hidden' => 'true']),
                        ['class' => 'btn btn-success btn-block']
                    ) ?>
                </div>
            <?php ActiveForm::end(); ?>
        <?php } ?>
	</div>
	<div id="content" class="col-sm-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
        ]); ?>
        <?php } ?>
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>
        <?= Alert::widget() ?>
        <table class="table table-bordered table-stripped table-hover table-condensed">
            <thead>
                <th style="width: 5%">№</th>
                <th><?= Yii::t('app', 'File') ?></th>
                <th style="width: 10%"><?= Yii::t('app', 'Act.') ?></th>
            </thead>
            <tbody>
                <?php foreach($fileList ?? [] as $key => $file) { ?>
                    <tr>
                        <td><?= $key + 1 ?></td>
                        <td><?= $file->original_name ?></td>
                        <td>
                            <?= Html::a(
                                Html::tag('i', '', ['class' => 'fa fa-download', 'aria-hidden' => 'true']),
                                [
                                    'document/download',
                                    'id' => $file->id,
                                ],
                                [
                                    'target' => '_blank',
                                    'title'  => Yii::t('app', 'Download'),
                                ]) ?>
                            <?php if (in_array($roleId, [3])) { ?>
                            <?= Html::a(
                                Html::tag('i', '', ['class' => 'fa fa-trash', 'aria-hidden' => 'true']),
                                [
                                    'document/delete',
                                    'id' => $file->id,
                                ],
                                [
                                    'title' => Yii::t('app', 'Delete'),
                                    'data-method' => 'POST',
                                ]) ?>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>