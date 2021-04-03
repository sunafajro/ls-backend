<?php

namespace school\models\reports;

use school\models\Report;
use school\models\Sale;
use school\models\Salestud;
use school\models\Student;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class SalesReport
 * @package school\models\reports
 */
class SalesReport extends Report
{
    public $page          = 1;
    public $limit         = 10;
    public $offset        = 0;
    public $pageCount     = 0;
    public $elementsCount = 0;

    /**
     * SalesReport constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $totalCount = (new Query())
            ->select('DISTINCT s.id')
            ->from(['s' => Sale::tableName()])
            ->innerJoin(['ss' => Salestud::tableName()], 'ss.calc_sale=s.id')
            ->innerJoin(['sn' => Student::tableName()], 'sn.id=ss.calc_studname')
            ->where(['s.visible' => 1, 'ss.visible' => 1, 'sn.active' => 1])
            ->count();
        $this->pageCount = ceil($totalCount/$this->limit);
        if (!empty($config['page']) && $config['page'] > 1 && $config['page'] <= $this->pageCount) {
            $this->offset = $this->limit * ($config['page'] - 1);
        } else {
            unset($config['page']);
        }
        parent::__construct($config);
    }

    /**
     * {@inheritDoc}
     */
    public function prepareReportData(): array
    {
        $sales = (new Query())
            ->select('s.id as sale_id, s.name as sale, s.value as value, s.procent as type')
            ->distinct()
            ->from(['s' => Sale::tableName()])
            ->innerJoin(['ss' => Salestud::tableName()], 'ss.calc_sale=s.id')
            ->innerJoin(['sn' => Student::tableName()], 'sn.id=ss.calc_studname')
            ->where(['s.visible' => 1, 'ss.visible' => 1, 'sn.active' => 1])
            ->orderBy(['s.procent' => SORT_DESC, 's.name' => SORT_ASC])
            ->limit($this->limit)
            ->offset($this->offset)
            ->all();

        $clients = [];
        if (!empty($sales)) {
            $this->elementsCount = count($sales);
            $clients = (new Query())
                ->select('sn.id as id, sn.name as name, ss.calc_sale as sale_id')
                ->from(['sn' => Student::tableName()])
                ->innerJoin(['ss' => Salestud::tableName()], 'ss.calc_studname=sn.id')
                ->where(['ss.visible' => 1, 'sn.active' => 1])
                ->andWhere(['in', 'calc_sale', ArrayHelper::getColumn($sales, 'sale_id')])
                ->orderby(['sn.name' => SORT_ASC])
                ->all();
        }

        return [$sales, $clients, [
            'page' => $this->page,
            'limit' => $this->limit,
            'offset' => $this->offset,
            'pageCount' => $this->pageCount,
            'elementsCount' => $this->elementsCount,
        ]];
    }
}