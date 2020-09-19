<?php
/**
 * @var View    $this
 * @var Student $student
 * @var int     $type
 * @var string  $userInfoBlock
 */

use app\models\Service;
use app\models\Student;
use app\widgets\filters\FiltersWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

$years = [];
for ($i = date('Y'); $i >= 2011; $i--) {
    $years[$i] = $i;
}

$services = [];
if (in_array($type, [
    Student::DETAIL_TYPE_INVOICES,
    Student::DETAIL_TYPE_LESSONS,
    Student::DETAIL_TYPE_INVOICES_LESSONS])
) {
    foreach (Service::getStudentServicesByInvoices([$student->id]) ?? [] as $service) {
        $services[$service['id']] = "#{$service['id']} {$service['name']}";
    }
}

if (Yii::$app->params['appMode'] === 'bitrix') {
    echo Html::tag('div', '', ['id' => 'main-menu']);
}
echo $userInfoBlock;

try {
    $items = [
        [
            'name'  => 'start',
            'title' => 'Начало периода',
            'type'  => FiltersWidget::FIELD_TYPE_DATE_INPUT,
            'value' => $params['start'] ?? '',
        ],
        [
            'name'  => 'end',
            'title' => 'Конец периода',
            'type'  => FiltersWidget::FIELD_TYPE_DATE_INPUT,
            'value' => $params['end'] ?? '',
        ],
    ];
    if (!empty($services)) {
        $items[] = [
            'name'  => 'service',
            'options' => $services ?? [],
            'prompt'  => Yii::t('app', '-all services-'),
            'title' => 'Услуги',
            'type'  => FiltersWidget::FIELD_TYPE_DROPDOWN,
            'value' => $params['service'] ?? '',
        ];
    }
    echo FiltersWidget::widget([
        'actionUrl' => Url::to(['studname/detail', 'id' => $student->id, 'type' => $type]),
        'items'     => $items,
    ]);
} catch (Exception $e) {
    echo Html::tag('div', 'Не удалось отобразить виджет. ' . $e->getMessage(), ['class' => 'alert alert-danger']);
}
?>
<h4><?= Yii::t('app', 'Hints') ?></h4>
<ul>
    <li>Колонка "количество занятий": количество выставленных в счете занятий складывается в итог, а проверенные занятия вычитаются из итога.</li>
    <li>Колонка "сумма": сумма по счету вычитается из итога, а сумма по оплате складывается в итог.</li>
</ul>
