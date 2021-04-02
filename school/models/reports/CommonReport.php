<?php

namespace school\models\reports;

use common\components\helpers\DateHelper;
use school\models\AccrualTeacher;
use school\models\Groupteacher;
use school\models\Invoicestud;
use school\models\Journalgroup;
use school\models\Moneystud;
use school\models\Office;
use school\models\Report;
use school\models\Service;
use school\models\Student;
use school\models\Studgroup;
use school\models\Studnorm;
use school\models\Timenorm;
use yii\db\Query;

/**
 * Class CommonReport
 * @package school\models\reports
 *
 * @property string $startDate
 * @property string $endDate
 */
class CommonReport extends Report
{
    /** @var string */
    public $startDate;
    /** @var string */
    public $endDate;

    /**
     * CommonReport constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        list($start, $end) = DateHelper::prepareMonthlyIntervalDates($config['startDate'] ?? null, $config['endDate'] ?? null);
        $config['startDate'] = $start;
        $config['endDate'] = $end;
        parent::__construct($config);
    }

    /**
     * {@inheritDoc}
     */
    public function prepareReportData(): array
    {
        #region офисы
        $offices = (new Query())
            ->select(['oid' => 'id', 'oname' => 'name'])
            ->from(Office::tableName())
            ->where(['visible' => 1])
            ->andWhere(['not in','id',['20','17','15','14','13']])
            ->orderBy(['name' => SORT_ASC])
            ->all();
        #endregion

        #region оплаты
        $commonPayments = (new Query())
            ->select('ms.calc_office as oid, SUM(value_card) as card, SUM(value_cash) as cash, SUM(value_bank) as bank, SUM(ms.value) as money')
            ->from(['ms' => Moneystud::tableName()])
            ->where(['ms.visible' => 1, 'ms.remain' => 0])
            ->andFilterWhere(['>=', 'ms.data', $this->startDate])
            ->andFilterWhere(['<=', 'ms.data', $this->endDate])
            ->groupby(['ms.calc_office'])
            ->all();
        #endregion

        #region счета
        $common_invoices = (new Query())
            ->select('is.calc_office as oid, SUM(is.value) as money, SUM(is.value_discount) as discount')
            ->from(['is' => Invoicestud::tableName()])
            ->where(['is.visible' => 1])
            ->andFilterWhere(['>=', 'is.data', $this->startDate])
            ->andFilterWhere(['<=', 'is.data', $this->endDate])
            ->groupby(['is.calc_office'])
            ->all();
        #endregion

        #region начисления
        $common_accruals = (new Query())
            ->select('gt.calc_office as oid, SUM(at.value) as money')
            ->from(['at' => AccrualTeacher::tableName()])
            ->leftjoin(['gt' => Groupteacher::tableName()], 'gt.id=at.calc_groupteacher')
            ->andFilterWhere(['>=', 'at.data', $this->startDate])
            ->andFilterWhere(['<=', 'at.data', $this->endDate])
            ->groupby(['gt.calc_office'])
            ->all();
        #endregion

        #region часы
        $online = Journalgroup::TYPE_ONLINE;
        $office = Journalgroup::TYPE_OFFICE;
        $common_hours = (new Query())
            ->select([
                'oid'          => 'gt.calc_office',
                'hours_online' => "SUM(CASE WHEN jg.type = '{$online}' THEN tn.value ELSE 0 END)",
                'hours_office' => "SUM(CASE WHEN jg.type = '{$office}' THEN tn.value ELSE 0 END)",
            ])
            ->from(['jg' => Journalgroup::tableName()])
            ->leftjoin(['gt' => Groupteacher::tableName()], 'gt.id=jg.calc_groupteacher')
            ->leftjoin(['s' => Service::tableName()], 's.id=gt.calc_service')
            ->leftjoin(['tn' => Timenorm::tableName()], 'tn.id=s.calc_timenorm')
            ->where(['jg.visible' => 1])
            ->andFilterWhere(['>=', 'jg.data', $this->startDate])
            ->andFilterWhere(['<=', 'jg.data', $this->endDate])
            ->groupby(['gt.calc_office'])
            ->all();
        #endregion

        #region студентов
        $subQuery = (new Query())
            ->select('count(DISTINCT sjg.calc_studname) as students')
            ->from('calc_studjournalgroup sjg')
            ->leftJoin(['jg' => Journalgroup::tableName()], 'jg.id=sjg.calc_journalgroup')
            ->leftJoin(['gt' => Groupteacher::tableName()], 'gt.id=jg.calc_groupteacher')
            ->where('gt.calc_office=o.id and jg.view=:vis and sjg.calc_statusjournal=:vis', [':vis'=>1])
            ->andFilterWhere(['>=', 'jg.data', $this->startDate])
            ->andFilterWhere(['<=', 'jg.data', $this->endDate]);

        $common_students = (new Query())
            ->select('o.id as oid')
            ->addSelect(['students'=>$subQuery])
            ->from(['o' => Office::tableName()])
            ->where('o.visible=:vis', [':vis'=>1])
            ->andWhere(['not in','o.id',['20','17','15','14','13']])
            ->all();
        #endregion

        /* получаем долги */
        $common_debts = [];
        foreach ($offices as $i => $office) {
            $tmp_debts = (new Query())
                ->select('s.debt as debts')
                ->from(['s' => Student::tableName()])
                ->leftjoin(['sg' => Studgroup::tableName()], 's.id=sg.calc_studname')
                ->leftjoin(['gt' => Groupteacher::tableName()], 'gt.id=sg.calc_groupteacher')
                ->where('sg.visible=:vis and s.debt<=:minus and gt.calc_office=:oid', [':vis'=>1, ':minus'=>0, ':oid'=>$office['oid']])
                ->groupby(['s.id'])
                ->all();
            if ($office['oid']!=6) {
                $tmp = 0.001;
                foreach($tmp_debts as $td) {
                    $tmp += $td['debts'];
                }
                $common_debts[$i]['oid'] = $office['oid'];
                $common_debts[$i]['debts'] = $tmp;
            }
        }

        /* задаем начальные переменные для формирования многомерного массива с данными для таблицы */
        /* оплаты */
        $pmnts = ['cash' => 0, 'card' => 0, 'bank' => 0, 'money' => 0];
        /* счета */
        $nvcs = 0;
        /* скидки */
        $dscnt = 0;
        /* начисления */
        $ccrls = 0;
        /* часы */
        $hrs = [
            'hours_online' => 0,
            'hours_office' => 0,
        ];
        /* долги */
        $dbts = 0;
        /* долги */
        $sts = 0;
        /* задаем начальные переменные для формирования многомерного массива с данными для таблицы */

        /* формируем основной массив с данными для отчета */
        foreach($offices as $i => $office) {
            // создаем вложенный массив и задаем id офиса
            $common_report[$i]['oid'] = $office['oid'];
            // задаем имя офиса
            $common_report[$i]['name'] = $office['oname'];
            // задаем дефолтное значение оплат по офису
            $common_report[$i]['payments'] = [];
            // задаем дефолтное значение счетов по офису
            $common_report[$i]['invoices'] = 0;
            // задаем дефолтное значение скидок по офису
            $common_report[$i]['discounts'] = 0;
            // задаем дефолтное значение начислений по офису
            $common_report[$i]['accruals'] = 0;
            // задаем дефолтное значение часов по офису
            $common_report[$i]['hours_online'] = 0;
            $common_report[$i]['hours_office'] = 0;
            // задаем дефолтное значение долгов по офису
            $common_report[$i]['debts'] = 0;
            // распечатываем массив с оплатами
            foreach($commonPayments as $pay) {
                // выбираем оплаты по id офиса
                if ($common_report[$i]['oid'] == $pay['oid']) {
                    // вносим сумму оплат по офису в массив
                    $common_report[$i]['payments'] = ['cash' => $pay['cash'], 'card' => $pay['card'], 'bank' => $pay['bank'], 'money' => $pay['money']];
                    // суммируем оплаты для поледущего получения итогового значения
                    $pmnts['cash'] += $pay['cash'];
                    $pmnts['card'] += $pay['card'];
                    $pmnts['bank'] += $pay['bank'];
                    $pmnts['money'] += $pay['money'];
                }
            }
            // распечатываем массив со счетами
            foreach($common_invoices as $inv) {
                // выбираем счета по id офиса
                if($common_report[$i]['oid'] == $inv['oid']) {
                    // вносим сумму счетов по офису в массив
                    $common_report[$i]['invoices'] = $inv['money'];
                    // вносим сумму скидок счетов по офису в массив
                    $common_report[$i]['discounts'] = $inv['discount'];
                    // суммируем счета для последующего получения итогового значения
                    $nvcs = $nvcs + $inv['money'];
                    // суммируем скидки для последующего получения итогового значения
                    $dscnt = $dscnt + $inv['discount'];
                }
            }
            // распечатываем массив с начислениями
            foreach($common_accruals as $acr) {
                // выбираем начисления по id офиса
                if($common_report[$i]['oid'] == $acr['oid']) {
                    // вносим сумму начислений по офису в массив
                    $common_report[$i]['accruals'] = $acr['money'];
                    // суммируем начисления для поледущего получения итогового значения
                    $ccrls = $ccrls + $acr['money'];
                }
            }
            // распечатываем массив с часами
            foreach($common_hours as $hr) {
                // выбираем часы по id офиса
                if($common_report[$i]['oid'] == $hr['oid']) {
                    // вносим сумму часов по офису в массив
                    $common_report[$i]['hours_online'] = $hr['hours_online'];
                    $common_report[$i]['hours_office'] = $hr['hours_office'];
                    // суммируем часы для последущего получения итогового значения
                    $hrs['hours_online'] = $hrs['hours_online'] + $hr['hours_online'];
                    $hrs['hours_office'] = $hrs['hours_office'] + $hr['hours_office'];
                }
            }
            // распечатываем массив со студентами
            foreach($common_students as $st) {
                // выбираем студентов по id офиса
                if($common_report[$i]['oid'] == $st['oid']) {
                    // вносим сумму студентов по офису в массив
                    $common_report[$i]['students'] = $st['students'];
                    // суммируем студентов для последущего получения итогового значения
                    $sts = $sts + $st['students'];
                }
            }
            // распечатываем массив с долгами
            foreach($common_debts as $db) {
                // выбираем часы по id офиса
                if($common_report[$i]['oid'] == $db['oid']) {
                    // вносим сумму часов по офису в массив
                    $common_report[$i]['debts'] = $db['debts'];
                    // суммируем долги для последущего получения итогового значения
                    $dbts = $dbts + $db['debts'];
                }
            }
        }
        /* формируем основной массив с данными для отчета */

        /* ставим число побольше чтобы точно не совпадало с id офисов */
        $i = 999;
        /* добавляем последний вложенный массив в котором будут итоговые суммарные значения по столбцам
        *  задаем id офиса - в данном случае совпадает с номером массива
        */
        $common_report[$i]['oid'] = $i;
        /* задаем имя массива */
        $common_report[$i]['name'] = 'Итого:';
        /* задаем итоговую сумму по оплатам */
        $common_report[$i]['payments'] = $pmnts;
        /* задаем итоговвую сумму по счетам */
        $common_report[$i]['invoices'] = $nvcs;
        /* задаем итоговую сумму по скидкам */
        $common_report[$i]['discounts'] = $dscnt;
        /* задаем итоговую сумму по начислениям */
        $common_report[$i]['accruals'] = $ccrls;
        /* задаем итоговую сумму по часам */
        $common_report[$i]['hours_online'] = $hrs['hours_online'];
        $common_report[$i]['hours_office'] = $hrs['hours_office'];
        /* задаем итоговую сумму по студентам */
        $common_report[$i]['students'] = $sts;
        /* задаем итоговую сумму по долгам */
        $common_report[$i]['debts'] = $dbts;

        return $common_report;
    }
}