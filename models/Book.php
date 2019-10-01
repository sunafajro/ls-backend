<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "books".
 *
 * @property integer $id
 * @property string  $name
 * @property string  $author
 * @property string  $isbn
 * @property string  $description
 * @property integer $user_id
 * @property string  $data
 * @property integer $visible
 * @property integer $book_publisher_id
 * @property integer $language
 */
class Book extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'books';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'author', 'isbn', 'book_publisher_id', 'language_id'], 'required'],
            [['name', 'author', 'isbn', 'description'], 'string'],
            [['user_id', 'visible', 'book_publisher_id', 'language_id'], 'integer'],
            [['visible'], 'default', 'value'=> 1],
            [['created_at'], 'default', 'value'=> date('Y-m-d')],
            [['user_id'], 'default', 'value'=> Yii::$app->user->identity->id],
            [['data'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                => '№',
            'name'              => Yii::t('app', 'Name'),
            'author'            => Yii::t('app', 'Author'),
            'isbn'              => Yii::t('app', 'ISBN'),
            'description'       => Yii::t('app', 'Description'),
            'user_id'           => Yii::t('app', 'User'),
            'data'              => Yii::t('app', 'Date'),
            'visible'           => 'Visible',
            'book_publisher_id' => Yii::t('app', 'Publisher'),
            'language_id'       => Yii::t('app', 'Language'),
        ];
    }

    public function delete()
    {
        $this->visible = 0;
        return $this->save();
    }
}
