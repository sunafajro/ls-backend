<?php

namespace client\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * Class LessonSearch
 * @package client\models\search
 *
 * @property int $id
 * @property string $date
 * @property string $teacherName
 * @property int $officeId
 * @property int $studentId
 * @property int $serviceId
 */
class LessonSearch extends Model
{
    /* @var int */
    public $id;
    /* @var string */
    public $date;
    /* @var string */
    public $teacherName;
    /* @var int */
    public $officeId;
    /* @var int */
    public $studentId;
    /* @var int */
    public $serviceId;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['id', 'officeId'], 'integer'],
            [['teacherName', 'date'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => '№',
            'date' => Yii::t('app', 'Date'),
            'groupName' => Yii::t('app', 'Group'),
            'teacherName' => Yii::t('app', 'Teacher'),
            'officeId' => Yii::t('app', 'Office'),
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params = []): ActiveDataProvider
    {
        $lt = 'l';
        $tt = 't';
        $gt = 'g';
        $st = 's';
        $sjt = 'sj';

        $this->load($params);

        $query = (new Query())
            ->select([
                'id' => "{$lt}.id",
                'type' => "{$lt}.type",
                'date' => "{$lt}.data",
                'teacherId' => "{$lt}.calc_teacher",
                'teacherName' => "{$tt}.name",
                'subject' => "{$lt}.description",
                'hometask' => "{$lt}.homework",
                'groupId' => "{$lt}.calc_groupteacher",
                'groupName' => "{$st}.name",
                'officeId' => "{$gt}.calc_office",
                'comments' => "{$sjt}.comments",
                'successes' => "{$sjt}.successes",
                'status' => "{$sjt}.calc_statusjournal",
            ])
            ->from([$lt => 'calc_journalgroup'])
            ->innerJoin([$tt => 'calc_teacher'], "{$tt}.id = {$lt}.calc_teacher")
            ->innerJoin([$gt => 'calc_groupteacher'], "{$gt}.id = {$lt}.calc_groupteacher")
            ->innerJoin([$st => 'calc_service'], "{$st}.id = {$gt}.calc_service")
            ->innerJoin([$sjt => 'calc_studjournalgroup'], "{$lt}.id = {$sjt}.calc_journalgroup")
            ->where([
                "{$st}.id" => $this->serviceId,
                "{$lt}.visible" => 1,
                "{$sjt}.calc_studname" => $this->studentId,
            ]);

        if ($this->validate()) {
            $query->andFilterWhere(["{$lt}.id" => $this->id]);
            $query->andFilterWhere(['like', "{$tt}.name", $this->teacherName]);
            $query->andFilterWhere(['like', "DATE_FORMAT({$lt}.data, \"%d.%m.%Y\")", $this->date]);
            if (($params['end'] ?? false) && ($params['start'] ?? false)) {
                $query->andFilterWhere(['>=', "{$lt}.data", $params['start']]);
                $query->andFilterWhere(['<=', "{$lt}.data", $params['end']]);
            }
            $query->andFilterWhere(["{$gt}.calc_office" => $this->officeId]);
        } else {
            $query->andWhere('0 = 1');
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
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