<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class BasePollQuestion
 * @package common\models
 *
 * @property integer $id
 * @property integer $poll_id
 * @property string $title
 * @property string $type
 * @property string $items
 * @property integer $visible
 *
 * @property-read BasePoll $poll
 * @property-read BasePollQuestionResponse[] $responses
 */
class BasePollQuestion extends ActiveRecord
{
    const TYPE_ONE_ANSWER = "one_answer";
    const TYPE_MULTIPLE_ANSWERS = 'multiple_answers';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%poll_questions}}';
    }

    /**
     * @return ActiveQuery
     */
    public function getPoll(): ActiveQuery
    {
        return $this->hasOne(BasePoll::class, ['id' => 'poll_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getResponses(): ActiveQuery
    {
        return $this->hasMany(BasePollQuestionResponse::class, ['poll_question_id' => 'id']);
    }
}