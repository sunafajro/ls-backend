<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_moneystud".
 *
 * @property integer $id
 * @property integer $visible
 * @property double $value
 * @property double $value_card
 * @property double $value_cash
 * @property double $value_bank
 * @property string $data
 * @property integer $user
 * @property integer $calc_studname
 * @property integer $calc_office
 * @property string $data_visible
 * @property integer $user_visible
 * @property string $receipt
 * @property integer $remain
 * @property string $data_remain
 * @property integer $user_remain
 * @property integer $collection
 * @property string $data_collection
 * @property integer $user_collection
 */
class Moneystud extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_moneystud';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['visible', 'value_card', 'value_cash', 'value_bank', 'value', 'data', 'user', 'calc_studname', 'calc_office'], 'required'],
            [['visible', 'user', 'calc_studname', 'calc_office', 'user_visible', 'remain', 'user_remain', 'collection', 'user_collection'], 'integer'],
            [['value'], 'number'],
            [['data', 'data_visible', 'data_remain', 'data_collection'], 'safe'],
            [['receipt'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'visible' => Yii::t('app', 'Visible'),
            'value' => Yii::t('app', 'Payment value'),
            'value_card' => Yii::t('app', 'Payment by card'),
            'value_cash' => Yii::t('app', 'Payment by cash'),
            'value_bank' => Yii::t('app', 'Payment by bank'),
            'data' => Yii::t('app', 'Data'),
            'user' => Yii::t('app', 'User'),
            'calc_studname' => Yii::t('app', 'Calc Studname'),
            'calc_office' => Yii::t('app', 'Office'),
            'data_visible' => Yii::t('app', 'Data Visible'),
            'user_visible' => Yii::t('app', 'User Visible'),
            'receipt' => Yii::t('app', 'Receipt'),
            'remain' => Yii::t('app', 'Remain'),
            'data_remain' => Yii::t('app', 'Data Remain'),
            'user_remain' => Yii::t('app', 'User Remain'),
            'collection' => Yii::t('app', 'Collection'),
            'data_collection' => Yii::t('app', 'Data Collection'),
            'user_collection' => Yii::t('app', 'User Collection'),
        ];
    }
    
    /* возвращает список оплат в рамках указанных диапазона времени и офиса */
    public static function getPayments($start = null, $end = null, $office = null)
    {
        $payments = (new \yii\db\Query())
        ->select(['id' => 'ms.id', 'studentId' => 'sn.id', 'student' => 'sn.name', 
        'manager' => 'u.name','sum' => 'ms.value', 'card' => 'ms.value_card', 'cash' => 'ms.value_cash', 
        'bank' => 'ms.value_bank', 'date' => 'ms.data', 'receipt' => 'ms.receipt', 
        'active' => 'ms.visible', 'remain' => 'ms.remain', 'office' => 'ms.calc_office'])
        ->from('calc_moneystud ms')
        ->leftjoin('calc_studname sn', 'sn.id=ms.calc_studname')
        ->leftJoin('user u', 'u.id=ms.user')
        ->andFilterWhere(['ms.calc_office' => $office])
        ->andFilterWhere(['>=', 'ms.data', $start])
        ->andFilterWhere(['<=', 'ms.data', $end])
        ->orderby(['ms.data'=>SORT_DESC, 'ms.id'=>SORT_DESC])
        ->all();

        return $payments;
    }

    /**
     *  метод возвращает массив с оплатами студента по его id
     */
    public static function getStudentPaymentById($sid)
    {
        $payments = (new \yii\db\Query())
        ->select('cms.id as pid, cms.data as pdate, cms.value as pvalue, u.name as uname, co.name as oname, cms.receipt as receipt, cms.visible as visible, u2.name as editor, cms.data_visible as edit_date, cms.remain as remain')
        ->from('calc_moneystud cms')
        ->leftJoin('user u','u.id=cms.user')
        ->leftJoin('user u2', 'u2.id=cms.user_visible')
        ->leftJoin('calc_office co','co.id=cms.calc_office')
        ->where('cms.calc_studname=:id',[':id'=>$sid])
        ->orderby(['cms.id'=>SORT_DESC])
        ->all();

        return $payments;
    }

    /* возвращает краткие данные по оплатам студента по его id */
    public static function getStudentPaymentByIdBrief($sid)
    {
        $payments = (new \yii\db\Query())
        ->select(['id' => 'm.id', 'date' => 'm.data', 'sum' => 'm.value', 'receipt' => 'm.receipt'])
        ->from('calc_moneystud m')
        ->where('m.calc_studname=:id AND m.visible=:one', [':id' => $sid, ':one' => 1])
        ->orderby(['m.data' => SORT_DESC])
        ->all();
        return $payments;
    }


    /**
     * метод считает и возвращает дату окончания баланса занятий по одной услуге
     * вызывается из StudnameController.php actonView
     * @param array $schedule
     * @param integer $sid
     * @param integer $cnt
     * @return string
     */
    public static function getNextPaymentDay($schedule, $sid, $cnt)
    {
        $days = static::getScheduleDayIds($schedule, $sid);
        if (!empty($days)) {
            $daynames = ['', 'monday', 'tuesday', 'Wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            $date = date('Y-m-d');
            $first_iteration = true;
            while($cnt > 0) {
                /* перый проход, для того чтобы вычесть занятия от текущей даты до конца недели */
                if ($first_iteration) {
                    foreach ($schedule as $s) {
                        if ($s['service_id'] == $sid && $s['day_id'] >= date('N')) {
                            /* если занятия сегодня */
                            if ($s['day_id'] == date('N')) {
                                /* если время занятия уже прошло */
                                if (date('H') <= substr($s['begin'], 0, 2) && date('i') < substr($s['begin'], 3, 2)) {
                                    $cnt = $cnt - 1;
                                    if ($cnt <= 0) {
                                        break;
                                    }
                                }
                            /* если занятия завтра, послезавтра и т.д. */
                            } else {
                                $date = date('Y-m-d', strtotime('next ' . $daynames[$s['day_id']], strtotime($date)));
                                $cnt = $cnt - 1;
                                if ($cnt <= 0) {
                                    break;
                                }
                            }
                        }                    
                    }
                    $first_iteration = false;
                /* в последующем идем последовательно от даты к дате */
                } else {
                    foreach ($schedule as $s) {
                        if ($s['service_id'] == $sid) {
                            $date = date('Y-m-d', strtotime('next ' . $daynames[$s['day_id']], strtotime($date)));
                            $cnt = $cnt - 1;
                            if ($cnt <= 0) {
                                break;
                            }
                        }
                    }
                }
            }
            return $date;
        } else {
            return 'none';
        }
    }

    /**
     * метод озвращает номера дней из массива
     * @param array $data
     * @return array
     */
    public static function getScheduleDayIds($schedule, $sid)
    {
        $result = [];
        
        foreach ($schedule as $s) {
            if ($s['service_id'] == $sid) {
                $result[] = $s;
            }
        }

        return $result;
    }

    /**
     * метод подменяет в строках идентификатор одного студента на идентификатор другого
     * вызывается из StudnameController.php actionMerge
     * @param integer $id1
     * @param integer $id2
     * @return boolean
     */
    public static function changeStudentId($id1, $id2)
    {
        $sql = (new \yii\db\Query())
        ->createCommand()
        ->update(self::tableName(), ['calc_studname' => $id1], ['calc_studname' => $id2])
        ->execute();

        return ($sql == 0) ? false : true;
    }
}
