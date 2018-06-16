<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_schedule".
 *
 * @property integer $id
 * @property integer $calc_teacher
 * @property integer $calc_groupteacher
 * @property integer $calc_office
 * @property integer $calc_cabinetoffice
 * @property integer $calc_denned
 * @property string $time_begin
 * @property string $time_end
 * @property integer $visible
 * @property integer $user
 * @property string $data
 */
class Schedule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_schedule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['calc_teacher', 'calc_groupteacher', 'calc_office', 'calc_cabinetoffice', 'calc_denned', 'time_begin', 'time_end', 'visible', 'user', 'data'], 'required'],
            [['calc_teacher', 'calc_groupteacher', 'calc_office', 'calc_cabinetoffice', 'calc_denned', 'visible', 'user'], 'integer'],
            [['time_begin', 'time_end', 'data'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'calc_teacher' => Yii::t('app','Teacher'),
            'calc_groupteacher' => Yii::t('app','Group'),
            'calc_office' => Yii::t('app','Office'),
            'calc_cabinetoffice' => Yii::t('app','Room'),
            'calc_denned' => Yii::t('app','Day of week'),
            'time_begin' => Yii::t('app','Start time'),
            'time_end' => Yii::t('app','End time'),
            'visible' => 'Visible',
            'user' => 'User',
            'data' => 'Data',
        ];
    }

    /**
     * метод возвращает расписание занятий студента
     * вызывается из StudnameController.php actionView
     * @param integer $id
     * @return array
     */
    public static function getStudentSchedule($sid)
    {
        $schedule = (new \yii\db\Query())
        ->select('s.id as lesson_id, gt.id as group_id, gt.calc_service as service_id, srv.name as service, s.calc_denned as day_id, dn.name as day, s.time_begin as begin, s.time_end as end')
        ->from('calc_schedule s')
        ->innerJoin('calc_groupteacher gt', 's.calc_groupteacher=gt.id')
        ->innerJoin('calc_studgroup sg', 'sg.calc_groupteacher=gt.id')
        ->innerJoin('calc_service srv', 'srv.id=gt.calc_service')
        ->innerJoin('calc_denned dn', 'dn.id=s.calc_denned')
        ->where('s.visible=:one AND gt.visible=:one AND sg.visible=:one AND sg.calc_studname=:sid', [':one' => 1, ':sid' => $sid])
        ->orderby(['s.calc_denned' => SORT_ASC, 's.time_begin' => SORT_ASC])
        ->all();

        return $schedule;
    }
}
