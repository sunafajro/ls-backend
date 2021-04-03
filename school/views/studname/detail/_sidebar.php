<?php
/**
 * @var View    $this
 * @var Student $student
 * @var int     $type
 */

use common\components\helpers\AlertHelper;
use school\models\Service;
use school\models\Student;
use school\widgets\filters\FiltersWidget;
use school\widgets\filters\models\FilterDateInput;
use school\widgets\filters\models\FilterDropDown;
use school\widgets\userInfo\UserInfoWidget;
use yii\helpers\Url;
use yii\web\View;

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

try {
    echo UserInfoWidget::widget();

    $items = [
        new FilterDateInput([
            'name'  => 'start',
            'title' => Yii::t('app', 'Period start'),
            'value' => $params['start'] ?? '',
        ]),
        new FilterDateInput([
            'name'  => 'end',
            'title' => Yii::t('app', 'Period end'),
            'value' => $params['end'] ?? '',
        ]),
    ];
    if (!empty($services)) {
        $items[] = new FilterDropDown([
            'name'  => 'service',
            'title' => Yii::t('app', 'Services'),
            'options' => $services ?? [],
            'prompt'  => Yii::t('app', '-all services-'),
            'value' => $params['service'] ?? '',
        ]);
    }
    echo FiltersWidget::widget([
        'actionUrl' => Url::to(['studname/detail', 'id' => $student->id, 'type' => $type]),
        'items'     => $items,
    ]);
} catch (Exception $e) {
    echo AlertHelper::alert('Не удалось отобразить виджет. ' . $e->getMessage());
}
?>
<h4><?= Yii::t('app', 'Hints') ?></h4>
<ul>
    <li>Колонка "количество занятий": количество выставленных в счете занятий складывается в итог, а проверенные занятия вычитаются из итога.</li>
    <li>Колонка "сумма": сумма по счету вычитается из итога, а сумма по оплате складывается в итог.</li>
</ul>
