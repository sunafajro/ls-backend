<?php

/**
 * @var Poll $poll
 */

use common\components\helpers\IconHelper;
use common\models\BasePoll as Poll;
use common\models\BasePollQuestion as PollQuestion;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

if (!empty($poll)) {
$js = <<<JS
$(document)
    .on('change', '.js--question-item', function(e) {
        if (!e.target.checked) {
            $(this).closest('.js--question-item-block')
                .find('.js--question-item-option').each(function() {
                    $(this).prop('checked', false);
                });
        }
    })
    .on('change', '.js--question-item-option', function(e) {
        if (e.target.checked) {
            $(this).closest('.js--question-item-block').find('.js--question-item:eq(0)').prop('checked', true);
        }
    });
JS;
$this->registerJS($js);
?>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <?= IconHelper::icon('question-circle', 'Опрос:')?> <?= $poll->title ?>
        </div>
        <div class="panel-body">
            <?php
                $form = ActiveForm::begin([
                    'method' => 'post',
                    'action' => Url::to(['site/save-poll-answers', 'id' => $poll->id]),
                ]);
                /** @var PollQuestion $question */
                foreach ($poll->questions as $question) {
                    echo $this->render('_question', ['question' => $question]);
                }
                echo Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'style' => 'margin-top: 1rem']);
                ActiveForm::end();
            ?>
        </div>
    </div>
<?php }