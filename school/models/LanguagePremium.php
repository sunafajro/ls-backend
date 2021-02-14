<?php

namespace school\models;

use Yii;
use school\models\Lang;
/**
 * This is the model class for table "language_premiums".
 *
 * @property integer $id
 * @property integer $language_id
 * @property integer $value
 * @property integer $user_id
 * @property string  $created_at
 * @property integer $visible
 */
class LanguagePremium extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'language_premiums';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['visible'],    'default', 'value' => 1],
            [['created_at'], 'default', 'value' => date('Y-m-d')],            
            [['user_id'],    'default', 'value' => Yii::$app->user->identity->id],
            [['language_id', 'value', 'user_id', 'visible'], 'integer'],
            [['created_at'], 'safe'],
            [['language_id', 'value', 'user_id', 'created_at', 'visible'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => Yii::t('app', 'ID'),
            'language_id' => Yii::t('app', 'Language'),
            'value'       => Yii::t('app', 'Value'),
            'user_id'     => Yii::t('app', 'User'),
            'created_at'  => Yii::t('app', 'Creation date'),
            'visible'     => Yii::t('app', 'Visible'),
        ];
    }

    public function delete()
    {
        $this->visible = 0;
        return $this->save(true, ['visible']);
    }

    public static function getLanguagePremiums($params = NULL)
    {
        $lp = (new yii\db\Query())
        ->select([
            'id'       => 'lp.id',
            'language' => 'l.name',
            'value'    => 'lp.value',
        ])
        ->from(['lp' => self::tableName()])
        ->innerJoin(['l' => Lang::tableName()], 'lp.language_id = l.id')
        ->where([
            'lp.visible' => 1,
            'l.visible'  => 1
        ])
        ->andFilterWhere(['not in', 'lp.id', $params])
        ->orderby(['l.name' => SORT_ASC, 'lp.value' => SORT_ASC])
        ->all();

        return [
            'columns' => [
                [
                    'id'   => 'id',
                    'name' => '№',
                    'show' => true
                ],
                [
                    'id'   => 'language',
                    'name' => Yii::t('app', 'Language'),
                    'show' => true
                ],
                [
                    'id'   => 'value',
                    'name' => Yii::t('app', 'Value'),
                    'show' => true
                ],
            ],
            'data'    => $lp
        ];
    }

    public static function getLanguagePremiumsSimple($params = NULL)
    {
        $result = [];
        $lps = self::getLanguagePremiums($params);
        if (!empty($lps)) {
            foreach ($lps['data'] as $lp) {
                $result[$lp['id']] = $lp['language'] . ' ' . $lp['value'] . ' р.';
            }
        }

        return $result;
    }
}
