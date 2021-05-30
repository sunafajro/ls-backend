<?php

/**
 * @var Poll $poll
 * @var integer $responseId
 */

use common\components\helpers\IconHelper;
use common\models\BasePoll as Poll;
use common\models\BasePollQuestion as PollQuestion;

if (!empty($poll)) {
    /** @var PollQuestion $question */
    foreach ($poll->questions as $question) {
        echo $this->render('_question', ['question' => $question]);
    }
}