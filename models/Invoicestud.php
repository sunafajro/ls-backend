<?php

namespace app\models;

use app\traits\StudentMergeTrait;
use Yii;

/**
 * This is the model class for table "calc_invoicestud".
 *
 * @property integer $id
 * @property integer $visible
 * @property string $data
 * @property integer $calc_service
 * @property integer $calc_studname
 * @property integer $calc_salestud
 * @property integer $calc_salestud_proc
 * @property integer $calc_office
 * @property integer $num
 * @property double $value
 * @property double $value_discount
 * @property integer $user
 * @property integer $done
 * @property string $data_done
 * @property string $data_visible
 * @property integer $user_done
 * @property integer $user_visible
 * @property double $cumdisc
 * @property string $cumdisc_name
 * @property integer $remain
 * @property integer $user_remain
 * @property string $data_remain
 */
class Invoicestud extends \yii\db\ActiveRecord
{
    use StudentMergeTrait;

    // обычный счет
    const TYPE_NORMAL = 0;
    // не засчитывается в сумму общего отчета и отчета по счетам
    const TYPE_REMAIN = 1;
    // не засчитывается в сумму общего отчета и отчета по счетам и в баланс клиента
    const TYPE_NETTING = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_invoicestud';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['visible', 'data', 'calc_service', 'calc_studname', 'calc_office', 'num', 'value', 'value_discount', 'user'], 'required'],
            [['visible', 'calc_service', 'calc_studname', 'calc_salestud', 'calc_salestud_proc', 'calc_sale', 'calc_office', 'num', 'user', 'done', 'user_done', 'user_visible', 'remain', 'user_remain'], 'integer'],
            [['data', 'data_done', 'data_visible', 'data_remain'], 'safe'],
            [['value', 'value_discount', 'cumdisc'], 'number'],
            [['cumdisc_name'], 'string']
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
            'data' => 'Data',
            'calc_service' => Yii::t('app','Service'),
            'calc_studname' => 'Calc Studname',
            'calc_salestud' => Yii::t('app','Ruble sale'),
            'calc_salestud_proc' => Yii::t('app','Procent sale'),
            'calc_office' => Yii::t('app', 'Office'),
            'num' => Yii::t('app', 'Lesson count'),
            'value' => 'Value',
            'value_discount' => 'Value Discount',
            'user' => 'User',
            'done' => 'Done',
            'data_done' => 'Data Done',
            'data_visible' => 'Data Visible',
            'user_done' => 'User Done',
            'user_visible' => 'User Visible',
            'cumdisc' => 'Cumdisc',
            'cumdisc_name' => 'Cumdisc Name',
            'remain' => Yii::t('app', 'Remain'),
            'user_remain' => 'User Remain',
            'data_remain' => 'Data Remain',
        ];
    }

    /**
     *  метод возвращает массив с счетами студента по его id
     */
    public static function getStudentInvoiceById($sid = null)
    {
        $invoices = (new \yii\db\Query())
        ->select('cis.id as iid, cis.visible as ivisible, uv.name as uvisible, cis.data_visible as dvisible, cis.done as idone, ud.name as udone, cis.data_done as ddone, cs.id as sid, cs.name as sname, cis.data as idate, cis.num as inum, cis.value as ivalue, u.name as uname, co.id as oid, co.name as oname, s3.name as perm_sale, s2.name as rub_sale, s1.name as proc_sale, cis.remain as remain')
        ->from('calc_invoicestud cis')
        ->leftJoin('calc_service cs','cis.calc_service=cs.id')
        ->leftJoin('user u','u.id=cis.user')
        ->leftJoin('user ud', 'ud.id=cis.user_done')
        ->leftJoin('user uv', 'uv.id=cis.user_visible')
        ->leftJoin('calc_salestud ss1', 'ss1.id=cis.calc_salestud')
        ->leftJoin('calc_sale s1', 's1.id=ss1.calc_sale')
        ->leftJoin('calc_salestud ss2', 'ss2.id=cis.calc_salestud_proc')
        ->leftJoin('calc_sale s2', 's2.id=ss2.calc_sale')
        ->leftJoin('calc_sale s3', 's3.id=cis.calc_sale')
        ->leftJoin('calc_office co','co.id=cis.calc_office')
        ->where('cis.calc_studname=:id',[':id'=>$sid])
        ->orderby(['cis.visible'=>SORT_DESC,'cis.done'=>SORT_ASC,'cis.id'=>SORT_DESC])
        ->all();

        return $invoices;
    }

    public static function getStudentInvoiceByIdBrief($sid)
    {
        $invoices = (new \yii\db\Query())
        ->select(['id' => 'i.id', 'name' => 's.name', 'date' => 'i.data', 'num' => 'i.num', 'sum' => 'i.value'])
        ->from('calc_invoicestud i')
        ->leftJoin('calc_service s','i.calc_service=s.id')
        ->where('i.calc_studname=:id AND i.visible=:one', [ 'id' => $sid, ':one' => 1])
        ->orderBy(['i.data' => SORT_DESC])
        ->all();
        return $invoices;
    }

    public function getInvoices(array $params = []) : array
    {
        $invoices = (new \yii\db\Query())
        ->select('is.id as iid, sn.id as sid, sn.name as sname, u.name as uname, is.value as money, is.visible as visible, is.done as done, is.num as num, is.calc_service as id, is.data as date, is.remain as remain')
        ->from(['is' => 'calc_invoicestud'])
        ->leftJoin(['u' => 'user'], 'u.id = is.user')
        ->leftJoin(['sn' => 'calc_studname'], 'sn.id = is.calc_studname')
        ->andFilterWhere(['is.calc_office' => $params['office'] ?? NULL ])
        ->andFilterWhere(['>=', 'is.data', $params['start'] ?? NULL])
        ->andFilterWhere(['<=', 'is.data', $params['end'] ?? NULL])
        ->orderby(['is.data' => SORT_DESC, 'is.id' => SORT_DESC])
        ->all();

        return $invoices;
    }

    /**
     * @deprecated
     * метод подменяет в строках идентификатор одного студента на идентификатор другого
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
