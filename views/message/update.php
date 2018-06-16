<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\CalcMessage */

$this->title = Yii::t('app','Update');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Messages'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app','Update');

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
<div class="calc-message-update">

    <?= $this->render('_form', [
        'model' => $model,
	'types'=>$types,
        'reciever' => $reciever,
    ]) ?>

</div>
