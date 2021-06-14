<?php


namespace school\models\searches;

use school\models\Office;
use school\models\Student;
use school\models\StudentGrade;
use school\models\Teacher;
use yii\data\ActiveDataProvider;

/**
 * Class StudentGradeSearch
 * @package school\models\searches
 *
 * @property string $startDate
 * @property string $endDate
 * @property int $studentId
 * @property string $studentName
 * @property string $studentBirthDate
 * @property string $teacherName
 * @property string $officeName
 */
class StudentGradeSearch extends StudentGrade
{
    /** @var string */
    public $startDate;
    /** @var string */
    public $endDate;
    /** @var integer */
    public $studentId;
    /** @var string */
    public $studentName;
    /** @var string */
    public $studentBirthDate;
    /** @var string */
    public $teacherName;
    /** @var string */
    public $officeName;

    /** {@inheritDoc} */
    public function rules(): array
    {
        return [
            [['startDate', 'endDate', 'description', 'studentName', 'teacherName', 'officeName', 'date'], 'string'],
            [['id'], 'integer'],
        ];
    }

    /** {@inheritDoc} */
    public function attributeLabels(): array
    {
        return array_merge(parent::attributeLabels(), [
            'studentBirthDate' => \Yii::t('app', 'Birthdate'),
            'teacherName' => \Yii::t('app', 'Teacher'),
            'officeName' => \Yii::t('app', 'Office'),
        ]);
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params = []): ActiveDataProvider
    {
        $sgt = self::tableName();
        $st = Student::tableName();
        $tt = Teacher::tableName();
        $ot = Office::tableName();

        $query = StudentGradeSearch::find()
            ->select([
                'id' => "{$sgt}.id",
                'date' => "{$sgt}.date",
                'description' => "{$sgt}.description",
                'studentId' => "{$sgt}.calc_studname",
                'studentName' => "{$st}.name",
                'studentBirthDate' => "{$st}.birthdate",
                'teacherName' => "{$tt}.name",
                'officeName' => "{$ot}.name",
                'score' => "{$sgt}.score",
            ])
            ->innerJoin($st, "{$st}.id = {$sgt}.calc_studname")
            ->leftJoin($tt, "{$tt}.id = {$sgt}.teacher_id")
            ->leftJoin($ot, "{$ot}.id = {$sgt}.office_id")
            ->active();

        $this->load($params);
        if ($this->validate()) {
            $query->andFilterWhere(["{$sgt}.id" => $this->id]);
            $query->andFilterWhere(['like', "lower({$st}.name)", mb_strtolower($this->studentName)]);
            $query->andFilterWhere(['like', "lower({$st}.teacherName)", mb_strtolower($this->teacherName)]);
            $query->andFilterWhere(['like', "lower({$st}.officeName)", mb_strtolower($this->officeName)]);
            $query->andFilterWhere(['>=', "{$sgt}.date", $this->startDate]);
            $query->andFilterWhere(['<=', "{$sgt}.date", $this->endDate]);
            $query->andFilterWhere(['like', "DATE_FORMAT({$sgt}.date, \"%d.%m.%Y\")", $this->date]);
        } else {
            $query->where('1 = 0');
        }

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['date' => SORT_ASC]],
        ]);
    }
}