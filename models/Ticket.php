<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_ticket".
 *
 * @property integer $id
 * @property string  $title
 * @property string  $body
 * @property integer $user
 * @property string  $data
 * @property integer $visible
 * @property integer $user_visible
 * @property string  $data_visible
 * @property integer $published
 * @property integer $executor
 * @property integer $edit
 * @property string  $data_edit
 * @property integer $user_edit
 * @property integer $calc_ticketstatus
 * @property string  $comment
 * @property string  $deadline
 * @property integer $closed
 * @property integer $user_closed
 * @property string  $data_closed
 */
class Ticket extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_ticket';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'body', 'deadline'], 'required'],
            [['title', 'body', 'comment'], 'string'],
            [['visible', 'user', 'published', 'executor', 'edit','user_edit', 'calc_ticketstatus', 'closed', 'user_closed'], 'integer'],
            [['data', 'data_visible', 'data_edit','data_closed', 'deadline'],'date','format'=>'yyyy-mm-dd'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => Yii::t('app', 'Title'),
			'body' => Yii::t('app', 'Description'),
            'user' => 'User',
			'data' => 'Date',
			'visible' => 'Visible',
            'user_visible' => 'Removed by User',
            'data_visible' => 'Date of Removal',
			'published' => Yii::t('app', 'Published'),
			'executor' => Yii::t('app', 'Executor'),
            'edit' => 'Edit',
			'data_edit' => 'Date of Edit',
			'user_edit' => 'Edited by User',
			'calc_ticketstatus' => Yii::t('app', 'Ticket status'),
			'comment' => Yii::t('app', 'Comment'),
			'deadline' => Yii::t('app', 'Deadline'),
			'closed' => 'Closed',
            'user_closed' => 'Closed by User',
            'data_closed' => 'Date of Close',
        ];
    }

    /**
    * метод используется для запроса количества выставленных, но еще не принятых задач.
    **/
    public static function getTasksCount()
    {
        $id = (int)Yii::$app->session->get('user.uid');
        $task = [];

        $task = (new \yii\db\Query())
        ->select('count(tr.id) as cnt')
        ->from('calc_ticket_report tr')
        ->innerJoin('calc_ticket t', 't.id=tr.calc_ticket')
        ->where('tr.calc_ticketstatus=:status and tr.user=:user and t.visible=:vis', [':status'=>5, ':user'=>(int)$id, 'vis'=>1])
        ->one();

        return (!empty($task)) ? $task['cnt'] : 0;
    }

    /**
    * метод используется для запроса одной выставленной, но еще не принятой задачи
    **/
    public static function getLastUnreadTask() 
    {
        $id = (int)Yii::$app->session->get('user.uid');
        $t = [];
        $ut = BaseUser::tableName();
        $task = (new \yii\db\Query())
        ->select('t.id as tid, t.title as title, t.body as text, u1.name as creator, u2.name as executor, ts.name as status, ts.color as color')
        ->from('calc_ticket_report tr')
        ->innerJoin('calc_ticket t', 't.id=tr.calc_ticket')
        ->innerJoin(['u1' => $ut], 'u1.id = t.user')
        ->innerJoin(['u2' => $ut], 'u2.id = tr.user')
        ->innerJoin('calc_ticketstatus ts', 'ts.id=tr.calc_ticketstatus')
        ->where('t.visible=:vis and tr.user=:user and tr.calc_ticketstatus=:status', [':vis'=>1, ':user' => (int)$id, ':status'=> 5])
        ->one();

        if(!empty($task)){
            $t = [
                'tid' => $task['tid'],
                'title' => $task['title'],
                'creator' => $task['creator'],
                'executor' => $task['executor'],
                'text' => $task['text'],
                'color' => 'text-' . $task['color'],
                'status' => $task['status'],
            ];
        }

        return !empty($t) ? $t : null;
    }
}
