<?php

/**
 * @var Poll $poll
 * @var integer $responseId
 * @var integer $studentId
 */

use common\models\BasePoll as Poll;
use common\models\BasePollQuestion as PollQuestion;
use yii\helpers\Html;

if (!empty($poll)) {
    /** @var PollQuestion $question */
    foreach ($poll->questions as $key => $question) {
        $blockId = "collapse_responses_{$studentId}";
        echo Html::a(
            'Развернуть/Свернуть ответы',
            "#{$blockId}",
            ['role' => 'button', 'data-toggle' => 'collapse', 'aria-expanded' => 'false', 'aria-controls' => $blockId]
        );
        echo Html::beginTag('div', ['class' => 'collapse', 'id' => $blockId]);
        $response = $question->getResponses()->andWhere(['poll_response_id' => $responseId, 'visible' => 1])->one();
        echo $this->render('_question', ['question' => $question, 'response' => $response]);
        echo Html::endTag('div');
    }
}