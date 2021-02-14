<?php

namespace school\models;

use school\models\queries\NewsQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "calc_news".
 *
 * @property integer $id
 * @property integer $visible
 * @property string  $date
 * @property integer $author
 * @property string  $subject
 * @property string  $body
 */
class News extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() : string
    {
        return 'calc_news';
    }

    /**
     * @inheritdoc
     */
    public function rules() : array
    {
        return [
            [['visible'], 'default', 'value' => 1],
            [['author'], 'default', 'value' => Yii::$app->user->identity->id],
            [['date'], 'default', 'value' => date('Y-m-d H:i:s')],
            [['visible', 'author'], 'integer'],
            [['subject', 'body'], 'string'],
            [['date'], 'safe'],
            [['visible', 'date', 'author', 'subject', 'body'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() : array
    {
        return [
            'id'      => 'ID',
            'visible' => 'Visible',
            'date'    => 'Date',
            'author'  => Yii::t('app', 'Author'),
            'subject' => Yii::t('app', 'Subject'),
            'body'    => Yii::t('app', 'Description'),
        ];
    }

    /**
     * @return NewsQuery|ActiveQuery
     */
    public static function find() : ActiveQuery
    {
        return new NewsQuery(get_called_class(), []);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser() : ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'author']);
    }
}
