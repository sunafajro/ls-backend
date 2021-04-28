<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class BasePollResponse
 * @package common\models
 *
 * @property integer $id
 * @property integer $poll_id
 * @property integer $visible
 * @property integer $user_id
 * @property string $created_at
 * @property string $deleted_at
 *
 * @property-read BasePoll[] $poll
 * @property-read BasePollQuestionResponse[] $questionResponses
 */
class BasePollResponse extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%poll_responses}}';
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
    public function getQuestionResponses(): ActiveQuery
    {
        return $this->hasMany(BasePollQuestionResponse::class, ['poll_response_id' => 'id']);
    }
}