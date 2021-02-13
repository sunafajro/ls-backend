<?php

namespace school\models\searches;

use school\models\User;
use school\models\Office;
use school\models\Student;
use school\models\StudentCommission;
use yii\data\ActiveDataProvider;

class StudentCommissionSearch extends StudentCommission
{
    public $id;
    public $date;
    public $studentName;
    public $userName;
    public $officeId;

    /**
     * @inheritdoc
     */
    public function rules() : array
    {
        return [
            [['id', 'officeId'], 'integer'],
            [['studentName', 'userName'], 'string'],
            [['date'], 'safe'],
        ];
    }

    public function search(array $params = []) : ActiveDataProvider
    {
        $this->load($params);
        $query = (new \yii\db\Query())
        ->select([
            'id'          => 'sc.id',
            'date'        => 'sc.date',
            'studentId'   => 'sc.student_id',
            'studentName' => 's.name',
            'percent'     => 'sc.percent',
            'debt'        => 'sc.debt',
            'value'       => 'sc.value',
            'userName'    => 'u.name',
            'officeId'    => 'sc.office_id',
            'officeName'  => 'o.name',
            'comment'     => 'sc.comment',
        ])
        ->from(['sc' => StudentCommission::tableName()])
        ->innerJoin(['s' => Student::tableName()], 's.id = sc.student_id')
        ->innerJoin(['u' => User::tableName()], 'u.id = sc.user_id')
        ->innerJoin(['o' => Office::tableName()], 'o.id = sc.office_id')
        ->where([
            'sc.visible' => 1,
        ])
        ->andFilterWhere(['sc.id' => $this->id])
        ->andFilterWhere(['like', 's.name', $this->studentName])
        ->andFilterWhere(['like', 'u.name', $this->userName])
        ->andFilterWhere(['sc.office_id' => $this->officeId]);

        if ($this->date) {
            $query->andFilterWhere(['like', 'DATE_FORMAT(sc.date, "%d.%m.%Y")', $this->date]);
        } else if ($params['end'] && $params['start']) {
            $query->andFilterWhere(['>=', 'sc.date', $params['start']]);
            $query->andFilterWhere(['<=', 'sc.date', $params['end']]);
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort'=> [
                'attributes' => [
                    'id',
                    'date',
                    'studentName',
                    'userName',
                    'officeId' => [
                        'asc' => ['o.name' => SORT_ASC],
                        'desc' => ['o.name' => SORT_DESC],
                    ],
                ],
                'defaultOrder' => [
                    'date' => SORT_DESC,
                ],
            ],
        ]);
    }
}