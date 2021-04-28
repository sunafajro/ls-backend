<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class BasePollQuestionResponse
 * @package common\models
 *
 * @property integer $id
 * @property integer $poll_id
 * @property integer $poll_question_id
 * @property integer $poll_response_id
 * @property string $items
 * @property integer $visible
 * @property integer $user_id
 * @property string $created_at
 * @property string $deleted_at
 *
 * @property-read BasePoll $poll
 * @property-read BasePollQuestion $question
 * @property-read BasePollResponse $response
 */
class BasePollQuestionResponse extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%poll_question_responses}}';
    }

    /**
     * @return ActiveQuery
     */
    public function getResponse(): ActiveQuery
    {
        return $this->hasOne(BasePollResponse::class, ['id' => 'poll_response_id']);
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
    public function getQuestion(): ActiveQuery
    {
        return $this->hasOne(BasePollQuestion::class, ['id' => 'poll_question_id']);
    }
}