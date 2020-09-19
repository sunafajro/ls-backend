<?php
/**
 * @var View              $this
 * @var Student           $student
 * @var ArrayDataProvider $detailData
 * @var array             $params
 * @var int               $type
 * @var string            $userInfoBlock
 */

// use app\models\Service;
use app\models\Student;
use app\widgets\Alert;
use yii\bootstrap\Tabs;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = 'Система учета :: '.Yii::t('app','Clients');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $student->name, 'url' => ['view', 'id' => $student->id]];
$this->params['breadcrumbs'][] = Yii::t('app','Detail');
?>
<div class="row row-offcanvas row-offcanvas-left student-detail-view">
    <div id="sidebar" class="col-xs-6 col-sm-2 sidebar-offcanvas">
        <?= $this->render('detail/_sidebar', [
                'student'       => $student,
                'params'        => $params,
                'type'          => $type,
                'userInfoBlock' => $userInfoBlock,
        ]) ?>
    </div>
    <div id="content" class="col-sm-10">
        <?php if (Yii::$app->params['appMode'] === 'bitrix') { ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [''],
            ]); ?>
        <?php } ?>
        <p class="pull-left visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">Toggle</button>
        </p>
        <?= Alert::widget() ?>
        <?php
            $items = [];
            foreach (Student::getDetailTypes() as $typeId => $typeName) {
                $items[] = [
                    'active'        => $type === $typeId,
                    'content'       => $type === $typeId
                        ? $this->render('detail/_table', [
                            'student'    => $student,
                            'detailData' => $detailData,
                            'type'       => $type,
                        ]) : '',
                    'label'   => $typeName,
                    'url'     => Url::to(['studname/detail', 'id' => $student->id, 'type' => $typeId]),
                ];
            }
            echo Tabs::widget([
                'items' => $items,
                'options' => ['style' => 'margin-bottom:10px']
            ]);
        ?>
    </div>
</div>