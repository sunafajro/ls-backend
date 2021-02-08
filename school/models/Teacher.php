<?php

namespace school\models;

use \yii\data\Pagination;

/**
 * Class Teacher
 * @package common\models
 *
 * @property-read User $user
 */
class Teacher extends \common\models\Teacher
{
    public static function getTeachers()
    {
        $teachers = (new \yii\db\Query())
        ->select([
            'id' => 't.id',
            'name' => 't.name'
        ])
        ->from(['t' => 'calc_teacher'])
        ->where([
            't.visible' => 1,
            't.old' => 0
        ])
        ->orderBy(['t.name' => SORT_ASC])
        ->all();

        return $teachers;
    }

    /* Метод возвращает список действующих преподавателей в виде одномерного массива */
    public static function getTeachersInUserListSimple()
    {
        $teachers = [];
        $tmp_teachers = static::getTeachers();

        if(
            !empty($tmp_teachers)
        ) {
            foreach($tmp_teachers as $t){
                $teachers[$t['id']] = $t['name'];
            }
        }
        return $teachers;
    }

    public static function getTeachersByAccruals($params = null)
    {
        $teachers = (new \yii\db\Query())
        ->select([
            'id' => 't.id',
            'name' => 't.name'
        ])
        ->distinct()
        ->from(['t' => 'calc_teacher'])
        ->innerJoin(['acc' => 'calc_accrualteacher'], 'acc.calc_teacher = t.id')
        ->where([
            'acc.visible' => 1,
            't.visible' => 1,
            't.old' => 0
        ])
        ->andFilterWhere(['<=', 'acc.data', isset($params['end']) ? $params['end'] : null])
        ->andFilterWhere(['>=', 'acc.data', isset($params['start']) ? $params['start'] : null])
        ->andFilterWhere(['t.id' => isset($params['id']) ? $params['id'] : null]);

        // делаем клон запроса
        $countQuery = clone $teachers;
        // получаем данные для паджинации
        $pages = new Pagination(['totalCount' => $countQuery->count()]);

        if (isset($params['limit'])) {
            $teachers = $teachers->limit($params['limit']);
        }

        if (isset($params['offset'])) {
            $teachers = $teachers->offset($params['offset']);
        }

        $teachers = $teachers->orderBy(['t.name' => SORT_ASC])
        ->all();

        return [
            'total' => $pages->totalCount,
            'rows' => $teachers
        ];
    }

    /* возвращает список преподавателей имеющих активные группы */
    public function getTeachersWithActiveGroups($tid = null)
    {
        $teachers = (new \yii\db\Query())
        ->select(['id' => 't.id', 'name' => 't.name'])
        ->distinct()
        ->from(['t' => 'calc_teacher'])
        ->leftjoin(['tg' => 'calc_teachergroup'], 'tg.calc_teacher = t.id')
        ->leftJoin(['gt' => 'calc_groupteacher'],'gt.id = tg.calc_groupteacher')
        ->where([
            't.visible' => 1,
            't.old' => 0,
            'gt.visible' => 1
        ])
        ->andFilterWhere(['t.id' => $tid])
        ->orderBy(['t.name' => SORT_ASC])
        ->all();
        return $teachers;
    }

    /* возвращает список активных групп преподавателя */
    public function getActiveTeacherGroups($tid = null)
    {
        $groups = (new \yii\db\Query())
        ->select('gt.id as id, s.name as name')
        ->from('calc_teachergroup tg')
        ->innerJoin('calc_groupteacher gt', 'tg.calc_groupteacher=gt.id')
        ->innerJoin('calc_service s','s.id=gt.calc_service')
        ->where('gt.visible=:vis and tg.calc_teacher=:tid', [':vis' => 1, ':tid' => $tid])
        ->orderBy(['s.name' => SORT_ASC])
        ->all();
        return $groups;
    }

    /* возвращает список преподавателей по которым есть занятия в расписании */
    public function getTeachersInSchedule()
    {
        $teachers = (new \yii\db\Query())
        ->select(['id' => 't.id', 'name' => 't.name'])
        ->distinct()
        ->from(['s' => 'calc_schedule'])
        ->innerJoin('calc_teacher t','s.calc_teacher=t.id')
        ->where([
            't.visible' => 1,
            't.old' => 0,
        ])
        ->orderBy(['t.name' => SORT_ASC])
        ->all();
        return $teachers;
    }
}
