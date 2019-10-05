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
 * @property string  $publisher
 * @property integer $language_id
 * @property integer $user_id
 * @property string  $created_at
 * @property integer $visible
 * 
 * @property Lang $language
 * @property User $user
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
            [['name', 'author', 'isbn', 'publisher', 'language_id'], 'required'],
            [['name', 'author', 'isbn', 'description', 'publisher'], 'string'],
            [['user_id', 'visible', 'language_id'], 'integer'],
            [['visible'],    'default', 'value'=> 1],
            [['created_at'], 'default', 'value'=> date('Y-m-d')],
            [['user_id'],    'default', 'value'=> Yii::$app->user->identity->id],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => '№',
            'name'        => Yii::t('app', 'Name'),
            'author'      => Yii::t('app', 'Author'),
            'isbn'        => Yii::t('app', 'ISBN'),
            'description' => Yii::t('app', 'Description'),
            'publisher'   => Yii::t('app', 'Publisher'),
            'language_id' => Yii::t('app', 'Language ID'),
            'user_id'     => Yii::t('app', 'User ID'),
            'created_at'  => Yii::t('app', 'Created at'),
            'visible'     => Yii::t('app', 'Active'),
        ];
    }

    public function delete()
    {
        $this->visible = 0;
        return $this->save();
    }

    public function getLanguage()
    {
        $this->hasOne(Lang::class, ['id', 'language_id']);
    }

    public function getUser()
    {
        $this->hasOne(User::class, ['id', 'user_id']);
    }
}
