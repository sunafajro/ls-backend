<?php
    use yii\helpers\Html;
    use yii\widgets\Breadcrumbs;
    $this->title = 'Система учета :: ' . Yii::t('app','Roles');
    $this->params['breadcrumbs'][] = [ 'url' => ['admin/index'], 'label' => Yii::t('app','Administration')];
    $this->params['breadcrumbs'][] = Yii::t('app','Roles');
?>

<div class="row row-offcanvas row-offcanvas-left admin-roles">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') : ?>
        <div id="main-menu"></div>
        <?php endif; ?>
        <?= $userInfoBlock ?>
        <div class="dropdown">
            <?= Html::button('<span class="fa fa-book" aria-hidden="true"></span> ' . Yii::t('app', 'Administration') . ' <span class="caret"></span>', ['class' => 'btn btn-default dropdown-toggle btn-sm btn-block', 'type' => 'button', 'id' => 'dropdownMenu', 'data-toggle' => 'dropdown', 'aria-haspopup' => 'true', 'aria-expanded' => 'true']) ?>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu">
              <?php foreach($links as $link) : ?>
                <li <?= $link['active'] ? 'class="active"' : '' ?>>
                    <?= Html::a($link['name'], [$link['url']], $link['classes'] ? ['class' => 'dropdown-item'] : '') ?>
                </li>
              <?php endforeach; ?>
            </ul>
        </div>
        <h4><?= Yii::t('app', 'Actions') ?></h4>
        <?= Html::a('<span class="fa fa-plus" aria-hidden="true"></span> ' . Yii::t('app', 'Add'), ['roles/create'], ['class' => 'btn btn-success btn-sm btn-block']) ?>
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
        <?php if(Yii::$app->session->hasFlash('error')) : ?>
        <div class="alert alert-danger" role="alert">
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
        <?php endif; ?>   
        <?php if(Yii::$app->session->hasFlash('success')) : ?>
        <div class="alert alert-success" role="alert">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
        <?php endif; ?>
        <table class="table table-striped table-hover table-condensed table-bordered small">
            <thead>
                <tr>
                    <th>№</th>
                    <th><?= Yii::t('app','Role') ?></th>
                    <th><?= Yii::t('app','Description') ?></th>
                    <th class="text-center"><?= Yii::t('app','Act.') ?></th>
            </thead>
            <?php $i = 1; ?>
            <?php foreach($roles as $r) : ?>
                <tr>
                <td><?= $i ?></td>
                <td><?= Yii::t('app', $r['name']) ?></td>
                <td><?= Yii::t('app', $r['description']) ?></td>
                <td class="text-center">
                    <?= Html::a('<span class="fa fa-pencil" aria-hidden="true"></span>',['roles/update', 'id'=>$r['id']]) ?>  
                    <?= Html::a('<span class="fa fa-trash" aria-hidden="true"></span>',['roles/delete', 'id'=>$r['id']]) ?>
                </td>
                </tr>
                <?php $i++; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>    
</div>