<?php

/**
 * @var View           $this
 * @var Student        $model
 * @var SpendSuccesses $spendSuccessesForm
 * @var array          $spendedSuccesses
 */

use school\models\SpendSuccesses;
use school\models\Student;
use common\widgets\alert\AlertWidget;
use school\widgets\sidebarButton\SidebarButtonWidget;
use school\widgets\userInfo\UserInfoWidget;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->title = Yii::$app->params['appTitle'] . Yii::t('app', 'Students') . ' :: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Clients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];;
$this->params['breadcrumbs'][] = "Списать успешики";
?>
<div class="row row-offcanvas row-offcanvas-left student-successes">
    <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 col-xl-2 sidebar-offcanvas">
        <?= UserInfoWidget::widget() ?>
        <h4>Текущий баланс: <?= Html::tag(
                'i',
                '',
                [
                    'class' => 'fa fa-ticket',
                    'title' => 'Баланс успешиков',
                    'aria-hidden' => 'true',
                ]) . $model->getSuccessesCount() ?></h4>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xl-10">
        <?= AlertWidget::widget() ?>
        <?= SidebarButtonWidget::widget() ?>
        <div>
            <h3>Списание "успешиков"</h3>
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($spendSuccessesForm, 'count')->input('number') ?>
            <?= $form->field($spendSuccessesForm, 'cause')->textarea(['rows' => 3]) ?>
            <?= Html::submitButton('Добавить', ['class' => 'btn btn-success']) ?>
            <?php ActiveForm::end(); ?>
        </div>
        <div>
            <h3>История:</h3>
            <?= GridView::widget([
                'dataProvider' => $spendedSuccesses,
                'columns' => [
                    'created_at' => [
                        'attribute' => 'created_at',
                        'label'     => $spendSuccessesForm->attributeLabel('created_at'),
                        'format'    => ['date', 'php:d.m.Y'],
                    ],
                    'count' => [
                        'attribute' => 'count',
                        'label'     => $spendSuccessesForm->attributeLabel('count'),
                    ],
                    'cause' => [
                        'attribute' => 'cause',
                        'label'     => $spendSuccessesForm->attributeLabel('cause'),
                    ],
                    'user_name' => [
                        'attribute' => 'user_name',
                        'label'     => $spendSuccessesForm->attributeLabel('user_id'),
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>