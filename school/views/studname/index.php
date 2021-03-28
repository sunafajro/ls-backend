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
 */

use common\components\helpers\IconHelper;
use school\assets\StudentListAsset;
use school\models\Auth;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\web\View;

$this->title = Yii::$app->name . ' :: ' . Yii::t('app','Clients');
$this->params['breadcrumbs'][] = Yii::t('app','Clients');
/** @var Auth $user */
$user   = Yii::$app->user->identity;
$roleId = $user->roleId;
$this->params['sidebar'] = [
    'offices' => $offices,
    'state' => $state,
    'oid' => $oid,
    'tss' => $tss,
    'roleId' => $roleId,
];
StudentListAsset::register($this);

// первый элемент страницы
$start = 1;
// последний элемент страницы
$end = 20;
// следующая страница
$nextpage = 2;
// предыдущая страница
$prevpage = 0;
// проверяем не задан ли номер страницы
if (Yii::$app->request->get('page')) {
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
if(!empty($students)) { ?>
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
                <?php if(In_array($roleId, [3, 4])) { ?>
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
                    <?php if (isset($student['description'])) { ?>
                        <div><?= Html::encode($student['description']) ?></div>
                    <?php } ?>
                    <?php if (isset($student['contracts']) && is_array($student['contracts']) && !empty($student['contracts'])) { ?>
                        <div style="margin-top: 0.5rem">
                            <?php foreach($student['contracts'] as $c) { ?>
                            <span style="display: block; font-style: italic; color: chocolate">Договор № <?= Html::encode($c['number']) ?> от <?= date('d.m.y', strtotime($c['date'])) ?> оформлен на <?= Html::encode($c['signer']) ?></span>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </td>
                <?php echo "<td class='text-center'>".($student['debt'] < 0 ? "<span class='label label-danger'>" : "<span class='label label-success'>").$student['debt']." р.</span></td>";
            // выводим ссылки на базовые действия для менеджера и руководителя
            if (in_array($roleId, [3, 4])) {
                echo "<td width='6%'>";
                // изменить информацию о студенте
                echo Html::a(IconHelper::icon('pencil'), ['studname/update','id'=>$student['stid']], ['title'=>Yii::t('app','Edit')]);
                echo " ";
                // для активного студента доступны кнопки добавления счета, начисления, звонка
                if($student['active']==1){
                    echo Html::a(IconHelper::icon('file'), ['invoice/index', 'sid'=>$student['stid']], ['title'=>Yii::t('app','Create invoice')]);
                    echo " ";
                echo Html::a(IconHelper::icon('rub'), ['moneystud/create', 'sid'=>$student['stid']], ['title'=>Yii::t('app','Create payment')]);
                echo "<br />";
                echo Html::a(IconHelper::icon('phone'), ['call/create', 'sid'=>$student['stid']], ['title'=>Yii::t('app','Create call')]);
                echo " ";
                }
                // если активный студент
                if($student['active']==1){
                    // выводим кнопку перевода в неактивное состояние
                echo Html::a(IconHelper::icon('times'), ['studname/inactive','id'=>$student['stid']], ['title'=>Yii::t('app', 'To inactive')]);
                } else {
                    // или наоборот
                    echo Html::a(IconHelper::icon('check'), ['studname/active','id'=>$student['stid']], ['title'=>Yii::t('app', 'To active')]);
                }
                if ($roleId === 4){
                    echo "</td>";
                }
            }
            // выводим ссылки на доп действия для руководителя
            if ($roleId === 3) {
                // удалить студента
                echo " ";
                echo Html::a(IconHelper::icon('trash'), ['studname/delete','id'=>$student['stid']], ['title'=>Yii::t('app', 'Delete')]);
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
<?php } else { ?>
    <p class="text-center"><img src="/images/404-not-found.jpg" class="rounded" alt="По вашему запросу ничего не найдено..."></p>
<?php }
