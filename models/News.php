<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_news".
 *
 * @property integer $id
 * @property integer $visible
 * @property string $date
 * @property integer $author
 * @property string $subject
 * @property string $body
 */
class News extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_news';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['visible', 'date', 'author', 'subject', 'body'], 'required'],
            [['visible', 'author'], 'integer'],
            [['date'], 'safe'],
            [['body'], 'string'],
            [['subject'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'visible' => 'Visible',
            'date' => 'Date',
            'author' => Yii::t('app', 'Author'),
            'subject' => Yii::t('app', 'Subject'),
            'body' => Yii::t('app', 'Description'),
        ];
    }
    
    public static function getNewsList($month = NULL, $year = NULL) 
    {
        $news = (new \yii\db\Query())
		->select('n.id as id, n.date as date, u.name as author, n.subject as subject, n.body as description')
		->from('calc_news n')
		->leftJoin(['u' => BaseUser::tableName()], 'u.id = n.author')
		->where('n.visible=:vis', [':vis'=>1])
        ->andFilterWhere(['MONTH(date)' => $month])
        ->andFilterWhere(['YEAR(date)' => $year])
		->orderby(['n.date'=>SORT_DESC])
		->all();
        
        return $news;
    }
}
