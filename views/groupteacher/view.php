<?php

use app\models\Groupteacher;
use app\models\Journalgroup;
use app\widgets\Alert;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use yii\web\View;

/**
 * @var View         $this
 * @var Groupteacher $model
 * @var Pagination   $pages
 * @var array        $checkTeachers
 * @var array        $groupInfo
 * @var array        $groupStudents
 * @var array        $lesattend
 * @var array        $lessons
 * @var int          $page
 * @var int          $state
 * @var array        $students
 * @var string       $userInfoBlock
 */

$this->title = Yii::$app->params['appTitle'] . ' Группа №' . $model->id;
$this->params['breadcrumbs'][] = Yii::t('app','Group') . ' №' . $model->id;
$this->params['breadcrumbs'][] = Yii::t('app', 'Journal');

$groupParams = [];
foreach($groupInfo as $key => $value) {
    $groupParams[] = Html::tag('b', $key . ':') . ' ' . $value;
}
$userRoleId = (int)Yii::$app->session->get('user.ustatus');
$userId = (int)Yii::$app->session->get('user.uid');
function getStudentOptions($lesson, $lessonBalance) {
    $options = [
        'class' => 'text-default',
        'data' => [
            'toggle' => 'tooltip',
            'placement' => 'top',
            'title' => 'Доступно занятий ' . ($lessonBalance ?? 0)
        ],
    ];
    if ((int)$lesson['jvisible'] === 1
        && (int)$lesson['jdone'] !== 1
        && (int)$lesson['jview'] === 0) {
            if ($lessonBalance === 0) {
                $options['class'] = 'text-danger';
            }
            return $options;
    } else {
        return [];
    }
}
?>
<div class="row row-offcanvas row-offcanvas-left group-index">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <div id="main-menu"></div>
        <?php } ?>
        <?= $userInfoBlock ?>
        <?php if ($model->visible == 1) { ?>
            <?php if (in_array($userRoleId, [3, 4, 10]) || $userId === 296 || array_key_exists(Yii::$app->session->get('user.uteacher'), $checkTeachers)) { ?>
                   <?= Html::a('<span class="fa fa-plus" aria-hidden="true"></span> '.Yii::t('app','Add lesson'), ['journalgroup/create','gid' => $model->id], ['class' => 'btn btn-default btn-block']) ?>
                <?php } ?>
            <?php foreach($items as $item) { ?>
                <?= Html::a($item['title'], $item['url'], $item['options']) ?>
            <?php } ?>
        <?php } ?>
        <h4><?= Yii::t('app', 'Filters') ?>:</h4>
        <?php $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['groupteacher/view', 'id'=>$model->id],
            ]); ?>
        <div class="form-group">
            <select class="form-control input-sm" name="status">
                <option value='all'><?= Yii::t('app','-all states-') ?></option>
                <option value="1" <?= $state == 1 ? ' selected' : '' ?>>На проверке</option>
                <option value="2" <?= $state == 2 ? ' selected' : '' ?>>Проверено</option>
                <option value="3" <?= $state == 3 ? ' selected' : '' ?>>Оплачено</option>
                <option value="4" <?= $state == 4 ? ' selected' : '' ?>>Исключено</option>
            </select>
        </div>
        <div class="form-group">
            <?= Html::submitButton('<span class="fa fa-filter" aria-hidden="true"></span> ' . Yii::t('app', 'Apply'), ['class' => 'btn btn-info btn-sm btn-block']) ?>
        </div>
        <?php ActiveForm::end(); ?>
        <h4>Параметры группы №<?= $model->id; ?></h4>
        <div class="well">
            <?= join(Html::tag('br'), $groupParams) ?>
        </div>
    </div>
	<div class="col-sm-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
            ]); ?>
        <?php } ?>

        <p class="pull-left visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>

        <?= Alert::widget() ?>

        <h4>Журнал группы №<?= $model->id ?></h4>
        <?php
            $pagerItems = [
                'previous' => [
                    'show'  => ($page > 1) && ((($page - 1) * 5) < $pages->totalCount),
                    'title' => Yii::t('app', 'Previous'),
                    'url'   => Url::to(['groupteacher/view', 'id' => $model->id, 'status' => $state ? $state : 'all', 'page'=>($page - 1)]),
                ],
                'next' => [
                    'show'  => ($page * 5) < $pages->totalCount,
                    'title' => Yii::t('app', 'Next'),
                    'url'   => Url::to(['groupteacher/view', 'id' => $model->id, 'status' => $state ? $state : 'all', 'page' => ($page + 1)]),
                ]
            ]; ?>
        <?= $this->render('_lesson_pager', [
                'items' => $pagerItems,
        ]) ?>
    <?php
    // распечатываем записи о занятиях
    foreach($lessons as $lesson){
        if ($lesson['jview'] == 0 && $lesson['jdone'] == 0 && $lesson['jvisible'] == 1) {
            $color = 'warning';
        } elseif ($lesson['jview'] == 1 && $lesson['jdone'] == 0 && $lesson['jvisible'] == 1) {
            $color = 'info';
        } elseif($lesson['jview'] == 1 && $lesson['jdone'] == 1 && $lesson['jvisible'] == 1) {
            $color = 'success';
        } elseif($lesson['jvisible'] == 0) {
            $color = 'danger';
        } else {
            $color = 'default';
        }
        echo Html::beginTag('div', ['class' => 'panel panel-' . $color]);
        echo Html::beginTag('div', ['class' => 'panel-heading']);
        switch ($lesson['type']) {
            case Journalgroup::TYPE_ONLINE:
                echo Html::tag(
                        'i',
                        null,
                        [
                            'class'       => 'fa fa-skype',
                            'aria-hidden' => 'true',
                            'style'       => 'margin-right: 5px',
                            'title'       => Yii::t('app', 'Online lesson'),
                        ]
                );
                break;
            case Journalgroup::TYPE_OFFICE:
                echo Html::tag(
                        'i',
                        null,
                        [
                            'class'       => 'fa fa-building',
                            'aria-hidden' => 'true',
                            'style'       => 'margin-right: 5px',
                            'title'       => Yii::t('app', 'Office lesson'),
                        ]
                );
                break;
        }
        switch ($lesson['edutime']){
            case 1: echo Html::img('/images/day.png',['title'=>Yii::t('app','Work time')]);break;
            case 2: echo Html::img('/images/night.png',['title'=>Yii::t('app','Evening time')]);break;
            case 3: echo Html::img('/images/halfday.png',['title'=>Yii::t('app','Halfday time')]);break;
        }
        if ($lesson['jview']==1) {
            echo " <span class='text-success' title='".Yii::t('app','Lesson viewed')."'>&#10003;</span>";
            if ($lesson['jdone']==0) {
                echo " <span class='text-danger' title='".Yii::t('app','Lesson undone')."'>&diams;</span>";
            } else {
                echo " <span class='text-info' title='".Yii::t('app','Lesson done')."'>&hearts;</span>";
            }
        }
        echo ($lesson['jvisible']!=1) ? " <del>" : "";
        echo " Занятие #".$lesson['jid']." от " . date('d.m.Y', strtotime($lesson['jdate'])) . ($lesson['time_begin'] !== '00:00' ? ' ' . $lesson['time_begin'] : '') . " (".Yii::t('app',date("l",strtotime($lesson['jdate']))).")";
        echo ($lesson['visible_date']!='0000-00-00') ? "</del> " : " ";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        $actions = [];
        if($lesson['jvisible']==1){
            if($lesson['jdone']!=1){
                if ($lesson['jview']==0) {
                    // занятие могут отредактировать только  преподаватель назначенный в группу, менеджер или руководитель
                    if (in_array($userRoleId, [3, 4, 10]) || array_key_exists(Yii::$app->session->get('user.uteacher'), $checkTeachers)) {
                        $actions[] = Html::a(Yii::t('app','Edit'), ['journalgroup/update', 'id' => $lesson['jid'], 'gid' => $model->id]);
                    }
                    // проверить занятие могут только менеджер или руководитель
                    if (in_array($userRoleId, [3, 4]) || $userId === 296) {
                        $actions[] = Html::a("Так и есть :)",['journalgroup/view','id' => $lesson['jid'], 'gid' => $model->id]);
                    }
                } else if((int)$lesson['jview'] === 1 && (in_array($userRoleId, [3, 4]) || $userId === 296)) {
                    // отменить проверку занятия могут только менеджер или руководитель
                    $actions[] = Html::a("Отменить 'проверено'",['journalgroup/unview','gid' => $model->id,'id' => $lesson['jid']]);
                }
                // занятие могут исключить только преподаватель назначенный в группу, менеджер или руководитель
                if (in_array($userRoleId, [3, 4, 10]) || array_key_exists(Yii::$app->session->get('user.uteacher'), $checkTeachers)) {
                    $actions[] = Html::a("Исключить из журнала",['journalgroup/delete','gid' => $model->id,'id' => $lesson['jid']]);
                }
            }
        } else {
            $actions[] = Html::a("Восстановить в журнал",['journalgroup/restore','gid'=>$model->id,'id'=>$lesson['jid']]);
            if (in_array($userRoleId, [3, 4])) {
                $actions[] = Html::a(
                        Yii::t('app', 'Delete'),
                        ['journalgroup/remove', 'gid' => $model->id, 'id' => $lesson['jid']],
                        [
                            'data-method' => 'post',
                            'data-confirm' => 'Вы действительно хотите полностью удалить запись из журнала?',
                        ]
                );
            }
        }
        echo join(' | ', $actions);
        echo Html::endTag('div');
        echo Html::beginTag('div', ['class' => 'panel-body']);
        echo "<p><strong>Описание:</strong> <br />".$lesson['jdesc']."</p>";
        echo "<p><strong>Д/з:</strong> <br />".$lesson['jhwork']."</p>";
        echo Html::beginTag('p', ['class' => 'small']);
        echo Html::tag('b', 'Преподаватель: ') . $lesson['tname'];
        echo Html::tag('br');
        echo Html::tag('span', Html::tag('b', 'Кем добавлено: ') . $lesson['uname'], ['class' => 'text-warning']);
        // выводим состояние занятия
        if ((int)$lesson['jvisible'] === 1) {
            echo Html::tag('br');
            $content = 'Статус: ';
            $content .= (int)$lesson['jdone'] === 1
                ? 'Начисление #' . $lesson['accrual'] . ', когда и кем: ' . date('d.m.Y', strtotime($lesson['done_date'])) . ', ' . $lesson['jduser']
                : ((int)$lesson['jview'] === 1 ? 'ожидает начисления' : 'ожидает проверки');
            echo Html::tag(
                'span',
                $content,
                ['class' => 'text-primary']
            );
        }
        // выводим информацию о проверке занятия
        if ($lesson['view_date'] !== '0000-00-00') {
            echo Html::tag('br');
            $content = (int)$lesson['jview'] === 1 ? 'Запись о занятии успешно проверена: ' : 'Запись о занятии снова на проверке: ';
            $content .= date('d.m.Y', strtotime($lesson['view_date'])) . ', кем: ' . $lesson['view_user'];
            echo Html::tag(
                'span',
                $content,
                ['class' => 'text-info']
            );
        }
        // выводим информацияю о исключении-восстановлении занятия
        if ($lesson['visible_date'] !== '0000-00-00') {
            echo Html::tag('br');
            $content = (int)$lesson['jvisible'] !== 1 ? 'Запись о занятии исключена из журнала: ' : 'Запись о занятии восстановлена в журнал: ';
            $content .= date('d.m.Y', strtotime($lesson['visible_date'])) . ', кем: ' . $lesson['jvuser'];
            echo Html::tag(
                'span',
                $content,
                ['class' => 'text-danger']
            );
        }
        if ($lesson['edit_date'] !== '0000-00-00') {
            echo Html::tag('br');
            $content = 'Состав занятия отредактирован: ' . date('d.m.Y', strtotime($lesson['edit_date'])) . ', кем: ' . $lesson['jeuser'];
            echo Html::tag(
                'span',
                $content,
                ['class' => 'text-success']
            );
        }
        echo Html::endTag('p');
        echo Html::beginTag('p', ['class' => 'small']);
        // для руководителей и менеджеров выводим ссылку на редактирование состава занятия
        if ((in_array($userRoleId, [3, 4])) && (int)$lesson['jview'] !== 1) {
            echo Html::a(
                "Редактировать состав занятия #" . $lesson['jid'],
                ['journalgroup/change','id' => $lesson['jid'], 'gid'=>$model->id],
                ['class' => 'small']
            );
        }
        // проверяем есть ли массив со списком присутствовавших студентов для занятия
        if (isset($lesattend[$lesson['jid']]['id'])) {
            // сверяем идентификаторы занятия между двумя массивами
            if ($lesattend[$lesson['jid']]['id']==$lesson['jid'] && isset($lesattend[$lesson['jid']]['p'])) {
                echo Html::tag('br');
                $arr = [];
                foreach ($students as $student) {
                    // проверяем что студент присутствовал на занятии
                    if ((int)$student['jid'] === (int)$lesson['jid'] && (int)$student['status'] === Journalgroup::STUDENT_STATUS_PRESENT) {
                        $successes = [];
                        if ($student['successes'] > 0) {
                            for ($num = 1; $num <= $student['successes']; $num++) {
                                $successes[] = Html::tag('i', '', ['class' => 'fa fa-ticket', 'aria-hidden' => 'true']);
                            }
                        }
                        $arr[] = '(' . Html::a(
                            $student['sname'],
                            ['studname/view', 'id' => $student['sid']],
                            getStudentOptions($lesson, $groupStudents[$student['sid']])
                        ) . join('', $successes) . ')';
                    }
                }
                echo "присутствовал: " . join(' ', $arr);
            }
        }
        if (isset($lesattend[$lesson['jid']]['id'])) {
            if ($lesattend[$lesson['jid']]['id']==$lesson['jid'] && isset($lesattend[$lesson['jid']]['a1'])) {
                echo Html::tag('br');
                $arr = [];
                foreach ($students as $student) {
                    if ((int)$student['jid'] === (int)$lesson['jid'] && (int)$student['status'] === Journalgroup::STUDENT_STATUS_ABSENT_WARNED) {
                        $arr[] = '(' . Html::a(
                            $student['sname'],
                            ['studname/view', 'id' => $student['sid']],
                            getStudentOptions($lesson, $groupStudents[$student['sid']])
                        ) . ')';
                    }
                }
                echo "не было (принес справку): " . join(' ', $arr);
            }
        }
        // распечатываем отсутствоваших не предупредивших
        if (isset($lesattend[$lesson['jid']]['id'])) {
            if ($lesattend[$lesson['jid']]['id']==$lesson['jid'] && isset($lesattend[$lesson['jid']]['a2'])) {
                echo Html::tag('br');
                $arr = [];
                foreach ($students as $student) {
                    if ((int)$student['jid'] === (int)$lesson['jid'] && (int)$student['status'] === Journalgroup::STUDENT_STATUS_ABSENT_UNWARNED){
                        $link = '(' . Html::a(
                            $student['sname'],
                            ['studname/view', 'id' => $student['sid']],
                            getStudentOptions($lesson, $groupStudents[$student['sid']])
                        );
                        if (in_array($userRoleId, [3, 4]) && (int)$lesson['jview'] === 1) {
                            $link .= ' ' . Html::a(
                                Html::tag('span', null, ['class' => 'fa fa-times', 'aria-hidden' => 'true']),
                                ['journalgroup/absent', 'id' => $lesson['jid'], 'studentId' => $student['sid']],
                                ['data-method' => 'post', 'title' => Yii::t('app', 'To absent (was ill)')]
                            );
                        }
                        $arr[] = $link . ')';
                    }
                }
                echo "не было: " . join(' ', $arr);
            }
        }
        echo Html::endTag('p');
        echo Html::endTag('div');
        echo Html::endTag('div');
    }
    ?>
        <?= $this->render('_lesson_pager', [
                'items' => $pagerItems,
        ]) ?>
    </div>
</div>
<?php
$js = <<< 'SCRIPT'
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
SCRIPT;
$this->registerJs($js);
?>