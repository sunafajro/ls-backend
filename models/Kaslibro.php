<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_kaslibro".
 *
 * @property integer $id
 * @property string $date
 * @property integer operation
 * @property string $operation_detail
 * @property integer $client
 * @property integer $executor
 * @property integer $month
 * @property integer $year
 * @property integer $office
 * @property integer $code
 * @property string $av_plus
 * @property string $b_plus
 * @property string $n_plus
 * @property string $av_minus
 * @property string $b_minus
 * @property string $n_minus
 * @property integer $user
 * @property integer $deleted
 * @property integer $reviewed
 * @property integer $done
 */
class Kaslibro extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_kaslibro';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date'], 'safe'],
            [['operation', 'client', 'executor', 'month', 'year', 'office', 'code', 'user', 'deleted', 'reviewed', 'done'], 'integer'],
            [['av_plus', 'b_plus', 'n_plus', 'av_minus', 'b_minus', 'n_minus'], 'number'],
            [['operation_detail'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => Yii::t('app','Date'),
            'operation' => Yii::t('app','Operation sense'),
            'operation_detail' => Yii::t('app','Operation detail'),
            'client' => Yii::t('app','Client'),
            'executor' => Yii::t('app','Executor'),
            'month' => Yii::t('app','Month'),
            'year' => Yii::t('app','Year'),
            'office' => Yii::t('app','Office'),
            'code' => Yii::t('app','Code'),
            'av_plus' => Yii::t('app','AV +'),
            'b_plus' => Yii::t('app','B +'),
            'n_plus' => Yii::t('app','Cash +'),
            'av_minus' => Yii::t('app','AV -'),
            'b_minus' => Yii::t('app','B -'),
            'n_minus' => Yii::t('app','Cash -'),
            'user' => Yii::t('app','User'),
            'deleted' => Yii::t('app','Deleted'),
            'reviewed' => Yii::t('app','Reviewed'),
            'done' => Yii::t('app','Done'),
        ];
    }

    /* Метод возвращает количество расходов ожидающих каких либо действий */
    public static function getExpensesCount()
    {
        $id = (int)Yii::$app->session->get('user.uid');
        $u = NULL;
        if(Yii::$app->session->get('user.ustatus')==3 || Yii::$app->session->get('user.ustatus')==8) {
            $rv = 0;
        } else {
            $rv = 1;
        }
        if(Yii::$app->session->get('user.ustatus')==4) {
            $dn = 0;
            $u = (int)$id;
        } else {
            $dn = NULL;
        }
        // Формируем массив с операциями
        $count = (new \yii\db\Query())
        ->select('count(id) as cnt')
        ->from('calc_kaslibro')
        ->where('deleted=:del', [':del'=>0])
        ->andFilterWhere(['reviewed' => $rv])
        ->andFilterWhere(['done' => $dn])
        ->andFilterWhere(['user' => $u])
        ->one();

        return (!empty($count)) ? $count['cnt'] : 0;
    }
}
