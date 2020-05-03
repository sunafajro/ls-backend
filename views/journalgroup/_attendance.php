<?php

use app\models\Journalgroup;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array $students
 */

$script = <<< JS
$(".js--student-status").on('change', function() {
    var _this = $(this);
    _this.closest('.row').find('.js--comment-for-student').prop('required', _this.val() === '1');
});
JS;
$this->registerJs($script, View::POS_READY);
?>
<?php foreach ($students ?? [] as $student) { ?>
    <div class="form-group field-calcstudjournalgroup-student_<?= $student['id'] ?>">
        <div class="row">
            <div class="col-sm-6">
                <label class="control-label" for="calcstudjournalgroup-comment_<?= $student['id'] ?>"><?= $student['name'] ?></label>
                <?= Html::input(
                    'text',
                    "Studjournalgroup[comment_{$student['id']}]",
                    $student['comment'] ?? '',
                    [
                        'id' => "calcstudjournalgroup-comment_{$student['id']}",
                        'class' => 'form-control js--comment-for-student',
                        'required' => true,
                    ]
                ) ?>
            </div>
            <div class="col-sm-3">
                <label class="control-label" for="calcstudjournalgroup-successes_<?= $student['id'] ?>">Получил "успешиков":</label>
                <?= Html::input(
                     'number',
                    "Studjournalgroup[successes_{$student['id']}]",
                    $student['successes'] ?? 0,
                    ['class' => 'form-control js--student-successes', 'id' => "calcstudjournalgroup-successes_{$student['id']}"]
                )?>
            </div>
            <div class="col-sm-3">
                <label class="control-label" for="calcstudjournalgroup-status_<?= $student['id'] ?>">Статус:</label>
                <?= Html::dropDownList(
                    "Studjournalgroup[status_{$student['id']}]",
                    Journalgroup::STUDENT_STATUS_PRESENT,
                    Journalgroup::getAttendanceScopedStatuses(),
                    ['class' => 'form-control js--student-status', 'id' => "calcstudjournalgroup-status_{$student['id']}"]
                )?>
            </div>
        </div>
    </div>
<?php } ?>