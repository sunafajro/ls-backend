<?php

/**
 * @var PollQuestion $question
 */

use common\models\BasePollQuestion as PollQuestion;

foreach ($question->items as $item) {
    echo $this->render('_item', ['item' => $item, 'questionId' => $question->id]);
}

