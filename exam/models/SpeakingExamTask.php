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
}