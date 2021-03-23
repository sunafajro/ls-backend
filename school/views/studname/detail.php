<?php
/**
 * @var View              $this
 * @var Student           $student
 * @var ArrayDataProvider $detailData
 * @var array             $params
 * @var int               $type
 */

use school\models\Student;
use common\widgets\alert\AlertWidget;
use school\widgets\sidebarButton\SidebarButtonWidget;
use yii\bootstrap\Tabs;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\web\View;

$this->title = 'Система учета :: '.Yii::t('app','Clients');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $student->name, 'url' => ['view', 'id' => $student->id]];
$this->params['breadcrumbs'][] = Yii::t('app','Detail');
?>
<div class="row row-offcanvas row-offcanvas-left student-detail">
    <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 col-xl-2 sidebar-offcanvas">
        <?= $this->render('detail/_sidebar', [
                'student'       => $student,
                'params'        => $params,
                'type'          => $type,
        ]) ?>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?= AlertWidget::widget() ?>
        <?= SidebarButtonWidget::widget() ?>
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