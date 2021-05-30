<?php

/**
 * @var PollQuestion $question
 * @var PollQuestionResponse $response
 */

use common\models\BasePollQuestion as PollQuestion;
use common\models\BasePollQuestionResponse as PollQuestionResponse;

$responseItems = $response->items;
foreach ($question->items as $item) {
    echo $this->render('_item', ['item' => $item, 'questionId' => $question->id, 'responseItem' => $responseItems[$item['id']] ?? []]);
}

