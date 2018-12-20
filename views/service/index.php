<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Система учета :: '.Yii::t('app','Services');
$this->params['breadcrumbs'][] = Yii::t('app','Services');
// определяем первый и последний элемент списка
if(Yii::$app->request->get('page')&&Yii::$app->request->get('page') > 0){
  $fitem = (25 * Yii::$app->request->get('page')) - 24;
  if(Yii::$app->request->get('page') == (ceil($pages->totalCount/25))){
    $litem = $pages->totalCount;
  }
  else {
    $litem = 25 * Yii::$app->request->get('page');
  }
}
else {
  $fitem = 1;
  if($pages->totalCount < 25){
    $litem = $pages->totalCount;
    }
  else {
  $litem = 25;
  }
}

$t = 'actual';

if (Yii::$app->request->get('type')) {
    $t = Yii::$app->request->get('type');
}

?>
<div class="row row-offcanvas row-offcanvas-left service-index">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= $userInfoBlock ?>
        <?php if ((int)Yii::$app->session->get('user.ustatus') === 3) : ?>
            <h4><?= Yii::t('app', 'Actions') ?></h4>
            <?= Html::a('<span class="fa fa-plus" aria-hidden="true"></span> ' . Yii::t('app', 'Add'), ['create'], ['class' => 'btn btn-success btn-sm btn-block']) ?>
        <?php endif; ?>
        <h4><?= Yii::t('app', 'Filters') ?></h4>
        <?php 
                $form = ActiveForm::begin([
                    'method' => 'get',
                    'action' => ['service/index'],
                    ]);
                    ?>
            <div class="form-group">
                <input type="text" class="form-control input-sm" placeholder="id или название..." name="TSS" value="<?= ($url_params['TSS'] != '') ? $url_params['TSS'] : '' ?>">
            </div>
            <div class="form-group">
                <select class="form-control input-sm" name="type">
                    <?php foreach($types as $key => $value): ?>
                    <option value="<?= $key ?>"<?= ($key === $t) ? ' selected' : '' ?>><?= mb_substr($value, 0, 13, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <select class="form-control input-sm" name="SCID">
                    <option value="all"><?= Yii::t('app', '-all cities-') ?></option>
                    <?php foreach($cities as $key => $value): ?>
                    <option value="<?= $key ?>"<?= ($key==Yii::$app->request->get('SCID')) ? ' selected' : '' ?>><?= mb_substr($value, 0, 13, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <select class="form-control input-sm" name="STID">
                    <option value="all"><?= Yii::t('app', '-all types-') ?></option>
                    <?php foreach($servicetypes as $key => $value): ?>
                    <option value="<?= $key ?>"<?= ($key==Yii::$app->request->get('STID')) ? ' selected' : '' ?>><?= mb_substr($value, 0, 13,'UTF-8') ?> </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <select class="form-control input-sm" name="SLID">
                    <option value="all"><?= Yii::t('app', '-all languages-') ?></option>
                    <?php foreach($languages as $key => $value): ?>
                    <option value="<?= $key ?>"<?= ($key==Yii::$app->request->get('SLID')) ? ' selected' : '' ?>><?= mb_substr($value, 0, 13,'UTF-8') ?> </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <select class="form-control input-sm" name="SAID">
                    <option value="all"><?= Yii::t('app', '-all ages-') ?></option>
                    <?php foreach($eduages as $key => $value): ?>
                    <option value="<?= $key ?>"<?= ($key==Yii::$app->request->get('SAID')) ? ' selected' : '' ?>><?= mb_substr($value, 0, 13,'UTF-8') ?> </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <select class="form-control input-sm" name="SFID">
                    <option value="all"><?= Yii::t('app', '-all forms-') ?></option>
                    <?php foreach($eduforms as $key => $value): ?>
                    <option value="<?= $key ?>"<?= ($key==Yii::$app->request->get('SFID')) ? ' selected' : '' ?>><?= mb_substr($value, 0, 13,'UTF-8') ?> </option>
                    <?php endforeach; ?>
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

        <?php if (Yii::$app->session->getFlash('success')) : ?>
            <div class='alert alert-success' role='alert'>
                <?= Yii::$app->session->getFlash('success') ?>
            </div>
        <?php endif; ?>  
        <?php if (Yii::$app->session->getFlash('error')) : ?>
            <div class='alert alert-danger' role='alert'>
                <?= Yii::$app->session->getFlash('error') ?>
            </div>
        <?php endif; ?>
        <?php if(!empty($services)) { ?>        
        <?php
            $prev = $url_params;
            $next = $url_params;
        ?>
        <div class="row" style="margin-bottom: 0.5rem">
            <div class="col-xs-12 col-sm-3 text-left">
                <?php if($url_params['page'] && $url_params['page'] > 1): ?>
                    <?php $prev['page'] = $prev['page'] - 1; ?>
                    <?= Html::a('<span aria-hidden="true">&larr;</span> ' . Yii::t('app', 'Previous'), $prev, ['class' => 'btn btn-default']) ?>
                <?php endif; ?>
            </div>
            <div class="col-xs-12 col-sm-6 text-center">
                <p style="margin-top: 1rem; margin-bottom: 0.5rem">Показано <?= $fitem ?> - <?= $litem ?> из <?= $pages->totalCount ?></p>
            </div>
            <div class="col-xs-12 col-sm-3 text-right">
                <?php if($url_params['page'] == NULL && (ceil($pages->totalCount/25)) > 1): ?>
                    <?php $next['page'] = 2; ?>
                    <?= Html::a(Yii::t('app', 'Next') .' <span aria-hidden="true">&rarr;</span>', $next, ['class' => 'btn btn-default']) ?>
                <?php endif; ?>
                <?php if($url_params['page'] != NULL && $url_params['page'] < (ceil($pages->totalCount/25))): ?>
                    <?php $next['page'] = $next['page'] + 1; ?>
                    <?= Html::a(Yii::t('app', 'Next') .' <span aria-hidden="true">&rarr;</span>', $next, ['class' => 'btn btn-default']) ?>
                <?php endif; ?>
            </div>
        </div>
        <table class="table table-stripped table-bordered table-hover table-condensed small" style="margin-bottom: 0.5rem">
            <thead>
                <th>ID</th>
                <th>Название</th>
                <th>Вид услуги</th>
                <th>1-занятие</th>
                <th>5-занятий</th>
                <th>8-занятий</th>
                <th>Длит.</th>
                <th>Дейст. до</th>
                <?php if(Yii::$app->session->get('user.ustatus')==3): ?>
                    <th>Ed/Del</th>
                <?php endif; ?>
            </thead>
            <tbody>
        <?php
             $city = 0;
             foreach($services as $service)
               {
                if($service['cid'] != $city)
                  {
                    echo "<tr class='info'>";
                    echo "<td colspan='9'>".$service['cname']."</td>";
                    echo "</tr>";
                    $city = $service['cid'];
                  }
                echo "<tr>";
                echo "<td>".$service['sid']."</td>";
                echo "<td>".$service['sname']."</td>";
                echo "<td>".$service['cstname']."</td>";
                echo "<td>".$service['cstnvalue']."</td>";
                echo "<td>".round($service['cstnvalue']*5)."</td>";
                echo "<td>".round($service['cstnvalue']*8)."</td>";
                echo "<td>".$service['ctnname']."</td>";
                echo "<td>".$service['sdate']."</td>";
                if(Yii::$app->session->get('user.ustatus')==3){
                    echo "<td>".Html::a('', ['service/update','id'=>$service['sid']],['class'=>'glyphicon glyphicon-pencil']);
                    echo " <span class='glyphicon glyphicon-remove' aria-hidden='true'></span></td>";
                }
                echo "</tr>";
               }
        ?>
            </tbody> 
        </table>
        <div class="row" style="margin-bottom: 0.5rem">
            <div class="col-xs-12 col-sm-3 text-left">
                <?php if($url_params['page'] && $url_params['page'] > 1): ?>
                    <?= Html::a('<span aria-hidden="true">&larr;</span> ' . Yii::t('app', 'Previous'), $prev, ['class' => 'btn btn-default']) ?>
                <?php endif; ?>
            </div>
            <div class="col-xs-12 col-sm-6 text-center">
                <p style="margin-top: 1rem; margin-bottom: 0.5rem">Показано <?= $fitem ?> - <?= $litem ?> из <?= $pages->totalCount ?></p>
            </div>
            <div class="col-xs-12 col-sm-3 text-right">
                <?php if($url_params['page'] == NULL && (ceil($pages->totalCount/25)) > 1): ?>
                    <?= Html::a(Yii::t('app', 'Next') .' <span aria-hidden="true">&rarr;</span>', $next, ['class' => 'btn btn-default']) ?>
                <?php endif; ?>
                <?php if($url_params['page'] != NULL && $url_params['page'] < (ceil($pages->totalCount/25))): ?>
                    <?= Html::a(Yii::t('app', 'Next') .' <span aria-hidden="true">&rarr;</span>', $next, ['class' => 'btn btn-default']) ?>
                <?php endif; ?>
            </div>
        </div>
        <?php } else { ?>
            <p class="text-center"><img src="/images/404-not-found.jpg" class="rounded" alt="По вашему запросу ничего не найдено..."></p>
        <?php } ?>
    </div>
</div>
