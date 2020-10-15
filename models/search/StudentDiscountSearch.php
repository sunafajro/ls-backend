<?php

namespace app\models\search;

use app\models\Sale;
use app\models\Salestud;
use app\models\Student;
use yii\data\ActiveDataProvider;

class StudentDiscountSearch extends Salestud
{
    public $student;
    public $user;

    public function rules()
    {
        return [
            [['student', 'user'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'date'     => 'Дата назначения',
            'student'  => 'Студент',
            'discount' => 'Скидка',
            'user'     => 'Кто назначил',
        ];
    }

    public function search(array $params = [])
    {
        $sdt = Salestud::tableName();
        $st = Student::tableName();
        $dt = Sale::tableName();
        $ut = 'user';

        $query = (new \yii\db\Query());
        $query->select([
            'id'         => 'sd.id',
            'date'       => 'sd.data',
            'student'    => 's.name',
            'studentId'  => 'sd.calc_studname',
            'discount'   => 'd.name',
            'discountId' => 'd.id',
            'user'       => 'u.name',
            'reason'     => 'sd.reason',
        ]);
        $query->from(['sd' => $sdt]);
        $query->innerJoin(['s' => $st], 's.id = sd.calc_studname');
        $query->innerJoin(['d' => $dt], 'd.id = sd.calc_sale');
        $query->innerJoin(['u' => $ut], 'u.id = sd.user');

        $query->where([
            'sd.visible'  => 1,
            'sd.approved' => 0,
        ]);

        $this->load($params);
        if ($this->validate()) {
            $query->andFilterWhere(['like', 's.name', $this->student]);
            $query->andFilterWhere(['like', 'u.name', $this->user]);
        } else {
            $query->andWhere('0 = 1');
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort'=> [
                'attributes' => [
                    'date',
                    'student',
                    'discount',
                    'user',
                ],
                'defaultOrder' => [
                    'date' => SORT_DESC
                ],
            ],
        ]);
    }
}