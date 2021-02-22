<?php

namespace exam\models;

use yii\base\Model;

/**
 * Class SpeakingExamTask
 * @package exam\models
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string[]|null $audio
 * @property string[] $images
 * @property string[] $questions
 * @property bool $sequentialQuestions
 * @property int $questionsInterval
 * @property bool $selectableImages
 * @property string $note
 * @property int $prepareTime
 * @property int $recordTime
 */
class SpeakingExamTask extends Model
{
    /** @var int */
    public $id;
    /** @var string */
    public $title;
    /** @var string */
    public $description;
    /** @var string[]|null */
    public $audio;
    /** @var string[] */
    public $images;
    /** @var string[] */
    public $questions;
    /** @var bool */
    public $sequentialQuestions;
    /** @var int */
    public $questionsInterval;
    /** @var bool */
    public $selectableImages;
    /** @var string */
    public $note;
    /** @var int */
    public $prepareTime;
    /** @var int */
    public $recordTime;

    /**
     * {@inheritDoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => \Yii::t('app', 'ID'),
            'title' => \Yii::t('app', 'Title'),
            'description' => \Yii::t('app', 'Description'),
            'audio' => \Yii::t('app', 'Audio task'),
            'images' => \Yii::t('app', 'Images'),
            'questions' => \Yii::t('app', 'Questions'),
            'sequentialQuestions' => \Yii::t('app', 'Need to answer the questions in order'),
            'questionsInterval' => \Yii::t('app', 'Question answer time'),
            'selectableImages' => \Yii::t('app', 'Need to select an image'),
            'note' => \Yii::t('app', 'Note'),
            'prepareTime' => \Yii::t('app', 'Prepare time'),
            'recordTime' => \Yii::t('app', 'Record time'),
        ];
    }
}