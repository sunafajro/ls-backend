<?php

namespace common\models;

use common\models\queries\BasePollQuery;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class BasePoll
 * @package common\models
 *
 * @property integer $id
 * @property string $entity_type
 * @property integer $active
 * @property string $title
 * @property integer $visible
 * @property integer $user_id
 * @property string $created_at
 * @property string $deleted_at
 *
 * @property-read BasePollQuestion[] $questions
 * @property-read BasePollResponse[] $responses
 */
class BasePoll extends ActiveRecord
{
    const ENTITY_TYPE_CLIENT = 'client';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%polls}}';
    }

    /**
     * @return BasePollQuery
     */
    public static function find(): BasePollQuery
    {
        return new BasePollQuery(get_called_class(), []);
    }

    /**
     * @return ActiveQuery
     */
    public function getQuestions(): ActiveQuery
    {
        return $this->hasMany(BasePollQuestion::class, ['poll_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getResponses(): ActiveQuery
    {
        return $this->hasMany(BasePollResponse::class, ['poll_id' => 'id']);
    }
}