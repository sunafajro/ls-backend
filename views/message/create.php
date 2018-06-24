<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CalcMessage */

$this->title = \Yii::t('app','Add');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app','Messages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

if(Yii::$app->session->get('user.ustatus')==5){
    $types = $cmwts;
    unset($cmwts);
} else {
    //составляем список типов сообщений
    foreach($cmwts as $cmwt){
        $temptypes[$cmwt['tid']]=$cmwt['tname'];
    }
    $types = array_unique($temptypes);
}
?>
<div class="calc-message-create">

    <!--<h1><?php /* Html::encode($this->title) */ ?></h1>-->

    <?= $this->render('_form', [
        'model' => $model,
	    'types' => $types,
    ]) ?>

</div>
