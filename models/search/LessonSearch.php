<?php

namespace app\models\search;

use Yii;
use app\models\Journalgroup;
use yii\data\ActiveDataProvider;

class LessonSearch extends Journalgroup
{
    public $id = NULL;
    public $date = NULL;
    public $teacherName = NULL;
    public $groupName = NULL;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'teacherId', 'groupId'], 'integer'],
            [['teacherName', 'groupName'], 'string'],
            [['date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'date' => Yii::t('app', 'date'),
            'groupName' => Yii::t('app', 'Group'),
            'teacherName' => Yii::t('app', 'Teacher'),
        ];
    }

    /**
     *  метод возвращает список занятий
     */
    public function search(array $params = []) : ActiveDataProvider
    {
        $this->load($params);
        $groupId = NULL;
        $groupName = NUll;
        if ((int)$this->groupName > 0) {
            $groupId = (int)$this->groupName;
        } else {
            $groupName = $this->groupName;
        }
        $query = (new \yii\db\Query())
        ->select([
            'id'          => 'l.id',
            'date'        => 'l.data',
            'teacherId'   => 'l.calc_teacher',
            'teacherName' => 't.name',
            'subject'     => 'l.description',
            'hometask'    => 'l.homework',
            'groupId'     => 'l.calc_groupteacher',
            'groupName'   => 's.name',
        ])
        ->from(['l' => static::tableName()])
        ->innerJoin(['t' => 'calc_teacher'], 't.id = l.calc_teacher')
        ->innerJoin(['g' => 'calc_groupteacher'], 'g.id = l.calc_groupteacher')
        ->innerJoin(['s' => 'calc_service'], 's.id = g.calc_service')
        ->where([
            'l.visible' => 1,
        ])
        ->andFilterWhere(['l.id' => $this->id])
        ->andFilterWhere(['like', 'DATE_FORMAT(l.data, "%d.%m.%Y")', $this->date])
        ->andFilterWhere(['like', 't.name', $this->teacherName])
        ->andFilterWhere(['like', 'g.id', $groupId])
        ->andFilterWhere(['like', 's.name', $groupName]);
        
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort'=> [
                'attributes' => [
                    'id',
                    'date',
                    'teacherName',
                    'groupName',
                    'subject',
                    'hometask',
                ],
                'defaultOrder' => [
                    'date' => SORT_DESC
                ],
            ],
        ]);
    }
}