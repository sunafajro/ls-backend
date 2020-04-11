<?php

namespace app\models\search;

use app\models\Groupteacher;
use Yii;
use app\models\Journalgroup;
use app\models\Service;
use app\models\Studjournalgroup;
use app\models\Teacher;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class LessonSearch extends Journalgroup
{
    /* @var int */
    public $id;
    /* @var string */
    public $date;
    /* @var string */
    public $teacherName;
    /* @var string */
    public $groupName;
    /* @var int */
    public $officeId;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'officeId'], 'integer'],
            [['teacherName', 'groupName', 'date'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => '№',
            'date'        => Yii::t('app', 'Date'),
            'groupName'   => Yii::t('app', 'Group'),
            'teacherName' => Yii::t('app', 'Teacher'),
            'officeId'    => Yii::t('app', 'Office'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function search(array $params = [], array $options = []) : ActiveDataProvider
    {
        $lt  = 'l';
        $tt  = 't';
        $gt  = 'g';
        $st  = 's';
        $sjt = 'sj';

        $this->load($params);

        if ((int)Yii::$app->session->get('user.ustatus') === 4) {
            if (!isset($params['LessonSearch']['officeId'])) {
                $this->officeId = (int)Yii::$app->session->get('user.uoffice_id');
            }
        }

        $groupId = NULL;
        $groupName = NUll;
        if ((int)$this->groupName > 0) {
            $groupId = (int)$this->groupName;
        } else {
            $groupName = $this->groupName;
        }

        $query = (new Query())
            ->select([
                'id'          => "{$lt}.id",
                'type'        => "{$lt}.type",
                'date'        => "{$lt}.data",
                'teacherId'   => "{$lt}.calc_teacher",
                'teacherName' => "{$tt}.name",
                'subject'     => "{$lt}.description",
                'hometask'    => "{$lt}.homework",
                'groupId'     => "{$lt}.calc_groupteacher",
                'groupName'   => "{$st}.name",
                'officeId'    => "{$gt}.calc_office"
            ])
            ->from([$lt => static::tableName()])
            ->innerJoin([$tt => Teacher::tableName()], "{$tt}.id = {$lt}.calc_teacher")
            ->innerJoin([$gt => Groupteacher::tableName()], "{$gt}.id = {$lt}.calc_groupteacher")
            ->innerJoin([$st => Service::tableName()], "{$st}.id = {$gt}.calc_service");
            if ($options['clientId'] ?? false) {
                $query->addSelect([
                        'comments' => "{$sjt}.comments",
                        'status'   => "{$sjt}.calc_statusjournal",
                    ])
                    ->innerJoin(
                        [$sjt => Studjournalgroup::tableName()],
                        "{$lt}.id = {$sjt}.calc_journalgroup AND {$sjt}.calc_studname = :clientId",
                        [':clientId' => $options['clientId']]);
            }
            if ($options['teacherId'] ?? false) {
                $query->where(["{$lt}.calc_teacher" => $options['teacherId']]);
            }
        $query->where(["{$lt}.visible" => 1]);

        if ($this->validate()) {
            $query->andFilterWhere(["{$lt}.id" => $this->id]);
            $query->andFilterWhere(['like', "{$tt}.name", $this->teacherName]);
            $query->andFilterWhere(["{$gt}.id" => $groupId]);
            $query->andFilterWhere(['like', "{$st}.name", $groupName]);
            $query->andFilterWhere(['like', "DATE_FORMAT({$lt}.data, \"%d.%m.%Y\")", $this->date]);
            $query->andFilterWhere(['>=', "{$lt}.data", $params['start']]);
            $query->andFilterWhere(['<=', "{$lt}.data", $params['end']]);
            $query->andFilterWhere(["{$gt}.calc_office" => $this->officeId]);
        } else {
            $query->andWhere('0 = 1');
        }
        
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $options['pageSize'] ?? 20,
            ],
            'sort'=> [
                'attributes' => [
                    'id',
                    'date',
                    'teacherName',
                    'groupName',
                ],
                'defaultOrder' => [
                    'date' => SORT_DESC
                ],
            ],
        ]);
    }
}