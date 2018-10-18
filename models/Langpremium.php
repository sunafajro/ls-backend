<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_langpremium".
 *
 * @property integer $id
 * @property integer $calc_lang
 * @property integer $value
 * @property integer $user
 * @property string $created_at
 * @property integer $visible
 */
class Langpremium extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_langpremium';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['calc_lang', 'value', 'user', 'created_at', 'visible'], 'required'],
            [['calc_lang', 'value', 'user', 'visible'], 'integer'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'calc_lang' => Yii::t('app', 'Language'),
            'value' => Yii::t('app', 'Value'),
            'visible' => Yii::t('app', 'Visible'),
        ];
    }

    public static function getLangPremiums($params = NULL)
    {
        $lp = (new yii\db\Query())
        ->select(['id' => 'lp.id', 'language' => 'l.name', 'value' => 'lp.value'])
        ->from('calc_langpremium lp')
        ->innerJoin('calc_lang l', 'lp.calc_lang=l.id')
        ->where('lp.visible=:one AND l.visible=:one', [':one' => 1])
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

    public static function getLangPremiumsSimple($params = NULL)
    {
        $result = [];
        $lps = self::getLangpremiums($params);
        if(!empty($lps)) {
            foreach($lps['data'] as $lp) {
                $result[$lp['id']] = $lp['language'] . ' ' . $lp['value'] . ' р.';
            }
        }

        return $result;
    }
}
