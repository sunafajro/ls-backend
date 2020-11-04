<?php

/**
 * @var View       $this
 * @var Pagination $pages
 * @var array      $offices
 * @var int        $oid
 * @var array      $services
 * @var string     $state
 * @var array      $students
 * @var string     $tss
 * @var string     $userInfoBlock
 */

use app\modules\school\assets\StudentListAsset;
use app\widgets\alert\AlertWidget;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app','Clients');
$this->params['breadcrumbs'][] = Yii::t('app','Clients');

StudentListAsset::register($this);

$roleId = Yii::$app->session->get('user.ustatus');
?>
<div class="row row-offcanvas row-offcanvas-left student-index">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?>
        <?php if (in_array($roleId, [3, 4])) { ?>
            <h4><?= Yii::t('app', 'Actions') ?>:</h4>
            <?= Html::a(
                    Html::tag('span', '', ['class' => 'fa fa-file-text-o', 'aria-hidden' => 'true'])
                    . ' ' . Yii::t('app', 'Receipt'),
                    ['receipt/common'],
                    ['class' => 'btn btn-default btn-sm btn-block']
                ) ?>
        <?php } ?>
        <h4><?= Yii::t('app', 'Filters') ?>:</h4>
        <?php
            $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['studname/index'],
            ]);
        ?>
        <div class="form-group">
            <input type="text" class="form-control input-sm" placeholder="имя или телефон..." name="TSS" value="<?= $tss != '' ? $tss : '' ?>">
        </div>
        <div class="form-group">
            <select class="form-control input-sm" name="STATE">
                <option value='all'><?= Yii::t('app','-all states-') ?></option>
                <option value="1"<?= (int)$state === 1 ? 'selected' : '' ?>>С нами</option>
                <option value="2"<?= (int)$state === 2 ? 'selected' : '' ?>>Не с нами</option>
            </select>
        </div>
        <div class="form-group">
            <select class="form-control input-sm" name="OID">
                <option value='all'><?= Yii::t('app','-all offices-') ?></option>
                <?php foreach($offices as $key => $value) { ?>
                    <option value="<?= $key ?>"<?= (int)$oid === (int)$key ? 'selected' : '' ?>><?= $value ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <?= Html::submitButton('<span class="fa fa-filter" aria-hidden="true"></span> ' . Yii::t('app', 'Apply'), ['class' => 'btn btn-info btn-sm btn-block']) ?>
        </div>
        <?php ActiveForm::end(); ?>

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

        <?= AlertWidget::widget() ?>

        <?php
            // первый элемент страницы
            $start = 1;
            // последний элемент страницы
            $end = 20;
            // следующая страница
            $nextpage = 2;
            // предыдущая страница
            $prevpage = 0;
            // проверяем не задан ли номер страницы
            if(Yii::$app->request->get('page')){
                    if(Yii::$app->request->get('page')>1){
                    // считаем номер первой строки с учетом страницы
                        $start = (20 * (Yii::$app->request->get('page') - 1) + 1);
                    // считаем номер последней строки с учетом страницы
                        $end = $start + 19;
                    // если страничка последняя подменяем номер последнего элемента
                    if($end>=$pages->totalCount){
                        $end = $pages->totalCount;
                    }
                    // считаем номер следующей страницы
                        $prevpage = Yii::$app->request->get('page') - 1;
                    // считаем номер предыдущей страницы
                        $nextpage = Yii::$app->request->get('page') + 1;
                    }
            }
        ?>
    <?php if(!empty($students)) : ?>
	<div class="row" style="margin-bottom: 0.5rem">
      <div class="col-xs-12 col-sm-3 text-left">
        <?= (($prevpage>0) ? Html::a(Yii::t('app', 'Previous'),['studname/index','page'=>$prevpage,'TSS'=>$tss,'STATE'=>$state, 'OID'=>$oid], ['class' => 'btn btn-default']) : '') ?>
      </div>
      <div class="col-xs-12 col-sm-6 text-center">
        <p style="margin-top: 1rem; margin-bottom: 0.5rem">Показано <?= $start ?> - <?= ($end>=$pages->totalCount) ? $pages->totalCount : $end ?> из <?= $pages->totalCount ?></p>
      </div>
      <div class="col-xs-12 col-sm-3 text-right">
        <?= (($end<$pages->totalCount) ? Html::a(Yii::t('app', 'Next'),['studname/index','page'=>$nextpage,'TSS'=>$tss,'STATE'=>$state, 'OID'=>$oid], ['class' => 'btn btn-default']) : '') ?>
      </div>
    </div>

    <table class="table table-striped table-bordered table-hover table-condensed small" style="margin-bottom: 0.5rem">
        <thead>
            <tr>
                <th class="text-center"><?= Yii::t('app', 'Sex') ?></th>
                <th><?= Yii::t('app', 'Name') ?></th>
                <th><?= Yii::t('app', 'Birthdate') ?></th>
                <th><?= Yii::t('app', 'Phone') ?></th>
                <th><?= Yii::t('app', 'Description') ?> / <?= Yii::t('app', 'Contract') ?></th>
                <th class="text-center"><?= Yii::t('app', 'Debt') ?></th>
                <?php if(Yii::$app->session->get('user.ustatus')==3 || Yii::$app->session->get('user.ustatus')==4) { ?>
                <th class="tbl-cell-5"><?= Yii::t('app', 'Act.') ?></th>
                <?php } ?>
            </tr>
        </thead>
        <?php foreach($students as $student) { ?>
            <tr class="<?= $student['debt'] < 0 ? 'danger' : '' ?>">
                <td class="text-center">
                    <span class="fa <?= (int)$student['stsex'] === 1 ? 'fa-male' : 'fa-female' ?>" aria-hidden="true"></span>
                </td>
                <td><?= Html::a('[#'.$student['stid'].'] '.$student['stname'], ['studname/view','id' => $student['stid']]) ?><br />
                    <p class="muted">
                        <?php foreach($services as $service) {
                            if ((int)$service['studentId'] === (int)$student['stid'] && !in_array($service['id'], $student['hiddenServices'])) {
                                echo "&nbsp;&nbsp;услуга <strong>#{$service['id']}</strong> {$service['name']} - осталось <strong>{$service['num']}</strong> занятий.<br />";
                            }
                        } ?>
                    </p>
                </td>
                <td>
                    <?= isset($student['birthdate']) && $student['birthdate'] !== '' && $student['birthdate'] !== '0000-00-00' ? date('d.m.y', strtotime($student['birthdate'])) : null ?>
                </td>
                <td><?= Html::encode($student['phone']) ?></td>
                <td>
                    <?php if (isset($student['description'])) : ?>
                    <div><?= Html::encode($student['description']) ?></div>
                    <?php endif; ?>
                    <?php if (isset($student['contracts']) && is_array($student['contracts']) && !empty($student['contracts'])) : ?>
                    <div style="margin-top: 0.5rem">
                        <?php foreach($student['contracts'] as $c) : ?>
                        <span style="display: block; font-style: italic; color: chocolate">Договор № <?= Html::encode($c['number']) ?> от <?= date('d.m.y', strtotime($c['date'])) ?> оформлен на <?= Html::encode($c['signer']) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </td>
                <?php echo "<td class='text-center'>".($student['debt'] < 0 ? "<span class='label label-danger'>" : "<span class='label label-success'>").$student['debt']." р.</span></td>";
	// выводим ссылки на базовые действия для менеджера и руководителя
	if (in_array($roleId, [3, 4])) {
            echo "<td width='6%'>";
            // изменить информацию о студенте
            echo Html::a('<span class="fa fa-pencil" aria-hidden="true"></span>', ['studname/update','id'=>$student['stid']], ['title'=>Yii::t('app','Edit')]);
            echo " ";
            // для активного студента доступны кнопки добавления счета, начисления, звонка
            if($student['active']==1){
                echo Html::a('<span class="fa fa-file" aria-hidden="true"></span>', ['invoice/index', 'sid'=>$student['stid']], ['title'=>Yii::t('app','Create invoice')]);
                echo " ";
	        echo Html::a('<span class="fa fa-rub" aria-hidden="true"></span>', ['moneystud/create', 'sid'=>$student['stid']], ['title'=>Yii::t('app','Create payment')]);
	        echo "<br />";
	        echo Html::a('<span class="fa fa-phone" aria-hidden="true"></span>', ['call/create', 'sid'=>$student['stid']], ['title'=>Yii::t('app','Create call')]);
	        echo " ";
            }
            // если активный студент
            if($student['active']==1){
                // выводим кнопку перевода в неактивное состояние
	        echo Html::a('<span class="fa fa-times" aria-hidden="true"></span>', ['studname/inactive','id'=>$student['stid']], ['title'=>Yii::t('app', 'To inactive')]);	
            } else {
                // или наоборот
                echo Html::a('<span class="fa fa-check" aria-hidden="true"></span>', ['studname/active','id'=>$student['stid']], ['title'=>Yii::t('app', 'To active')]);
            }
	    if(Yii::$app->session->get('user.ustatus')==4){
	        echo "</td>";
	    }
        }
        // выводим ссылки на доп действия для руководителя
        if(Yii::$app->session->get('user.ustatus')==3){
            // удалить студента
            echo " ";
            echo Html::a('<span class="fa fa-trash" aria-hidden="true"></span>', ['studname/delete','id'=>$student['stid']], ['title'=>Yii::t('app', 'Delete')]);
            echo "</td>";
        }
        echo "</tr>";
    }
    ?>
    </table>
	<div class="row" style="margin-bottom: 0.5rem">
      <div class="col-sm-3 text-left">
        <?= (($prevpage>0) ? Html::a(Yii::t('app', 'Previous'),['studname/index','page'=>$prevpage,'TSS'=>$tss,'STATE'=>$state, 'OID'=>$oid], ['class' => 'btn btn-default']) : '') ?>
      </div>
      <div class="col-sm-6 text-center">
        <p style="margin-top: 1rem; margin-bottom: 0.5rem">Показано <?= $start ?> - <?= ($end>=$pages->totalCount) ? $pages->totalCount : $end ?> из <?= $pages->totalCount ?></p>
      </div>
      <div class="col-sm-3 text-right">
        <?= (($end<$pages->totalCount) ? Html::a(Yii::t('app', 'Next'),['studname/index','page'=>$nextpage,'TSS'=>$tss,'STATE'=>$state, 'OID'=>$oid], ['class' => 'btn btn-default']) : '') ?>
      </div>
    </div>
    <?php else : ?>
        <p class="text-center"><img src="/images/404-not-found.jpg" class="rounded" alt="По вашему запросу ничего не найдено..."></p>
    <?php endif; ?>
    </div>
</div>
