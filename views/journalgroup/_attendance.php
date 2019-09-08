<?php

use app\models\Journalgroup;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var array $students
 */

$script = <<< JS
$(".js--student-status").on('change', function() {
    var _this = $(this);
    _this.closest('.row').find('.js--comment-for-student').prop('required', _this.val() === '1');
});
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
<?php foreach ($students as $student) { ?>
    <div class="form-group field-calcstudjournalgroup-student_<?= $student['id'] ?>">
        <label class="control-label" for="calcstudjournalgroup-comment_<?= $student['id'] ?>"><?= $student['name'] ?></label>
        <div class="row">
            <div class="col-sm-6">
                <?= Html::input(
                    'text',
                    'CalcStudjournalgroup[comment_' . $student['id'] . ']',
                    '',
                    [
                        'id' => 'calcstudjournalgroup-comment_' . $student['id'],
                        'class' => 'form-control js--comment-for-student',
                        'required' => true,
                    ]
                ) ?>
            </div>
            <div class="col-sm-6">
                <?= Html::dropDownList(
                    'CalcStudjournalgroup[status_' . $student['id'] . ']',
                    Journalgroup::STUDENT_STATUS_PRESENT,
                    Journalgroup::getAttendanceScopedStatuses(),
                    ['class' => 'form-control js--student-status', 'id' => 'calcstudjournalgroup-status_' . $student['id']]
                )?>
            </div>
        </div>
    </div>
<?php } ?>