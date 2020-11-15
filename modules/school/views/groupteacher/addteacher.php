<?php

use app\models\Teachergroup;
use app\modules\school\assets\ChangeGroupParamsAsset;
use app\models\Groupteacher;
use app\widgets\alert\AlertWidget;
use app\widgets\groupInfo\GroupInfoWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\View;

/**
 * @var View         $this
 * @var ActiveForm   $form
 * @var Groupteacher $group
 * @var Teachergroup $model
 * @var array        $check_teachers
 * @var array        $items
 * @var array        $params
 * @var array        $teachers
 * @var string       $userInfoBlock
 */

ChangeGroupParamsAsset::register($this);

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'List of teachers');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Group').' №' . $params['gid'], 'url' => ['groupteacher/view', 'id' => $params['gid']]];
$this->params['breadcrumbs'][] = Yii::t('app', 'List of teachers');
$roleId = Yii::$app->session->get('user.ustatus');
?>
<div class="row row-offcanvas row-offcanvas-left group-add-teacher">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= $userInfoBlock ?>
        <?php if($params['active'] == 1) { ?>
            <?php if(in_array($roleId, [3, 4]) || array_key_exists(Yii::$app->session->get('user.uteacher'), $check_teachers)) { ?>
                <?= Html::a(
                        Html::tag('span', '', ['class' => 'fa fa-plus', 'aria-hidden' => 'true']) . ' ' . Yii::t('app','Add lesson'),
                        ['journalgroup/create','gid' => $params['gid']],
                        ['class' => 'btn btn-default btn-block']
                    ) ?>
            <?php } ?>
            <?php foreach($items as $item) { ?>
                <?= Html::a($item['title'], $item['url'], $item['options']) ?>
            <?php } ?>
        <?php } ?>
        <?= GroupInfoWidget::widget(['group' => $group]) ?>
    </div>
	<div class="col-sm-10">
		<p class="pull-left visible-xs">
			<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle nav</button>
        </p>
        
        <?= AlertWidget::widget() ?>

        <?php if (in_array($roleId, [3, 4])) { ?>
            <h4><?= Yii::t('app', 'Add teacher to group') . ' #' . $params['gid'] ?></h4>
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'calc_teacher')->dropDownList($teachers, ['prompt'=>Yii::t('app', '-select-')]) ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Add'), ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        <?php } ?>     
	
        <h4><?php echo Yii::t('app', 'Current teachers of the group'); ?></h4>
        <table class="table">
            <thead>
                <tr>
                    <th><?php echo Yii::t('app', 'Teacher'); ?></th>
                    <th><?php echo Yii::t('app', 'When added'); ?></th>
                    <th><?php echo Yii::t('app', 'Who added'); ?></th>
                    <th><?php echo Yii::t('app', 'Actions'); ?></th>
                </tr>
            <tbody>
            <?php foreach ($curteachers as $curteacher) { ?>
                <?php switch ($curteacher['visible']) {
                    case 0: echo "<tr class='danger'>"; break;
                    case 1: echo "<tr class='success'>"; break;
                    default: echo "<tr>"; break;
                } ?>
                <td>
                <?php if (in_array($roleId, [3, 4])) { ?>
                    <?= Html::a($curteacher['teacher'], ['teacher/view','id' => $curteacher['id']]) ?>
                <?php } else {  ?>
                    <?= $curteacher['teacher'] ?>
                <?php } ?>
                    <?= (int)$group->calc_teacher === (int)$curteacher['id']
                            ? Html::tag('span', '', ['class' => 'fa fa-star', 'aria-hidden' => 'true', 'title' => 'Основной преподаватель'])
                            : '' ?>
                </td>
                <td><?= $curteacher['date'] ?></td>
                <td><?= $curteacher['user'] ?></td>
                <td>
                <?php if (in_array($roleId, [3, 4])) { ?>
                    <?php switch ($curteacher['visible']) {
                        case 0:
                            echo Html::a(
                                    '',
                                    ['groupteacher/restoreteacher', 'gid' => $params['gid'], 'tid' => $curteacher['id']],
                                    [
                                        'class' => 'fa fa-check',
                                        'title' => 'Восстановить преподавателя',
                                    ]
                                );
                            break;
                        case 1:
                            echo (int)$group->calc_teacher !== (int)$curteacher['id']
                                ? Html::a(
                                    Html::tag('i', '', ['class' => 'fa fa-star-o']),
                                    'javascript:void(0)',
                                    [
                                        'class' => 'js--change-group-params-btn',
                                        'style' => 'margin-right: 5px',
                                        'title' => 'Назначить основным',
                                        'data' => [
                                            'url' => Url::to(['groupteacher/change-params', 'id' => $params['gid'], 'name' => 'calc_teacher', 'value' => $curteacher['id']]),
                                        ],
                                    ]
                                ) : '';
                            echo Html::a(
                                    '',
                                    ['groupteacher/delteacher', 'gid' => $params['gid'], 'tid' => $curteacher['id']],
                                    [
                                        'class' => 'fa fa-times',
                                        'title' => 'Удалить преподавателя',
                                    ]
                                );
                            break;
                        default:
                            echo '';
                    } ?>
                <?php } ?>
                </td>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>