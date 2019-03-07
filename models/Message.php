<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calc_message".
 *
 * @property integer $id
 * @property integer $visible
 * @property integer $longmess
 * @property string $name
 * @property string $description
 * @property string $files
 * @property integer $user
 * @property string $data
 * @property integer $send
 * @property integer $calc_messwhomtype
 * @property string $refinement
 * @property integer $refinement_id
 */
class Message extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calc_message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'user', 'calc_messwhomtype', 'refinement', 'refinement_id'], 'required'],
            [['visible', 'longmess', 'user', 'send', 'calc_messwhomtype', 'refinement_id'], 'integer'],
            [['name', 'description', 'files', 'refinement'], 'string'],
            [['data'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'longmess' => 'Longmess',
            'name' => Yii::t('app','Message title'),
            'description' => Yii::t('app','Message text'),
            'files' => 'Files',
            'user' => 'User',
            'data' => 'Data',
            'send' => 'Send',
            'calc_messwhomtype' => Yii::t('app','For whom'),
            'refinement' => 'Refinement',
            'refinement_id' => Yii::t('app','Reciever'),
        ];
    }

    public static function getMessageById($id)
    {
        $message = (new \yii\db\Query())
        ->select([
            'id' => 'm.id',
            'title' => 'm.name',
            'text' => 'm.description',
            'date' => 'm.data',
            'destination_type' => 'm.calc_messwhomtype',
            'sender_id' => 'm.user',
            'reciever_id' => 'm.refinement_id',
            'files' => 'm.files',
            'sended' => 'm.send'
        ])
        ->from(['m' => 'calc_message'])
        ->where([
            'm.id' => $id
        ])
        ->one();
        if ($message) {
            $receiver = NULL;
            $sender = NULL;
            if (
                (int)$message['destination_type'] === 5
                || (int)$message['destination_type'] === 100
            ) {
                $receiver = (new \yii\db\Query())
                ->select(['id' => 'id', 'name' => 'name'])
                ->from(['u' => 'user'])
                ->where([
                    'id' => $message['reciever_id']
                ])
                ->one();
            } else if ((int)$message['destination_type'] === 13) {
                $receiver = (new \yii\db\Query())
                ->select(['id' => 'id', 'name' => 'name'])
                ->from(['s' => 'calc_studname'])
                ->where([
                    'id' => $message['reciever_id']
                ])
                ->one();
            } else {
                $receiver = (new \yii\db\Query())
                ->select(['id' => 'id', 'name' => 'name'])
                ->from(['mwt' => 'calc_messwhomtype'])
                ->where([
                    'id' => $message['destination_type']
                ])
                ->one();
            }
            if (
                (int)$message['destination_type'] === 100
            ) {
                $sender = (new \yii\db\Query())
                ->select(['id' => 'id', 'name' => 'name'])
                ->from(['s' => 'calc_studname'])
                ->where([
                    'id' => $message['sender_id']
                ])
                ->one();
            } else {
                $sender = (new \yii\db\Query())
                ->select(['id' => 'id', 'name' => 'name'])
                ->from(['u' => 'user'])
                ->where([
                    'id' => $message['sender_id']
                ])
                ->one();
            }
            if ($receiver !== null) {
                $message['receiver'] = $receiver['name'];
            }
            if ($sender !== null) {
                $message['sender'] = $sender['name'];
            }
            $response = (new \yii\db\Query())
            ->select(['id' => 'id'])
            ->from(['mr' => 'calc_messreport'])
            ->where([
                'calc_message' => $message['id'],
                'ok' => 0,
                'user' => Yii::$app->session->get('user.uid')
            ])
            ->one();
            if ($response !== null) {
                $message['response'] = $response['id'];
            }
        }
        return $message;
    }

    public static function getUnreadedMessagesIds()
    {
        $unreaded = (new \yii\db\Query())
        ->select(['id' => 'mr.calc_message', 'response_id' => 'mr.id'])
        ->from(['mr' => 'calc_messreport'])
        ->innerJoin(['m' => 'calc_message'], 'mr.calc_message = m.id')
        ->where([
            'mr.send' => 1,
            'm.send' => 1,
            'mr.user' => Yii::$app->session->get('user.uid'),
            'mr.ok' => 0,
            'm.visible' => 1
        ])
        ->all();
        $messages_ids = [];
        foreach ($unreaded as $nrd) {
            $messages_ids[$nrd['response_id']] = $nrd['id'];
        }
        return $messages_ids;
    }

    public static function getUserMessages($start = NULL, $end = NULL, $direction = 'in')
    {
        $messages = (new \yii\db\Query()) 
        ->select([
            'id' => 'm.id',
            'title' => 'm.name', 
            'text' => 'm.description',
            'files' => 'm.files',
            'sender_id' => 'm.user',
            'sender_emp_name' => 'u1.name',
            'sender_stn_name' => 'sn1.name',
            'date' => 'm.data',
            'sended' => 'm.send',
            'destination_id' => 'm.calc_messwhomtype',
            'destination_name' => 'mwt.name',
            'receiver_id' => 'm.refinement_id',
            'receiver_emp_name' => 'u2.name',
            'receiver_stn_name' => 'sn2.name'
        ])
        ->from(['m' => 'calc_message'])
        ->leftJoin(['u1' => 'user'], 'u1.id = m.user')
        ->leftJoin(['sn1' => 'calc_studname'], 'sn1.id = m.user')
        ->leftJoin(['u2' => 'user'], 'u2.id = m.refinement_id')
        ->leftJoin(['sn2' => 'calc_studname'], 'sn2.id = m.refinement_id')
        ->innerJoin(['mwt' => 'calc_messwhomtype'], 'mwt.id = m.calc_messwhomtype');
        if($direction === 'in') {
            $messages = $messages->innerJoin(['mr' => 'calc_messreport'], 'mr.calc_message = m.id');
            $messages = $messages->where([
                'mr.user' => Yii::$app->session->get('user.uid'),
                'm.visible' => 1
            ]);
        } 
        if($direction === 'out') {
            if((int)Yii::$app->session->get('user.ustatus') === 3) {
                $messages = $messages->where([
                    'm.visible' => 1
                ])
                ->andWhere([
                    'not', [
                        'and',
                        ['m.calc_messwhomtype' => 5],
                        ['m.refinement_id' => Yii::$app->session->get('user.uid')]
                    ]
                ]);
            } else {
                $messages = $messages->where([
                    'm.user' => Yii::$app->session->get('user.uid'),
                    'm.visible' => 1
                ]);
            }
        }
        $messages = $messages->andFilterWhere(['>=', 'm.data', $start]);
        $messages = $messages->andFilterWhere(['<=', 'm.data', $end]);
        $messages = $messages->andFilterWhere(['in', 'm.id', $unreaded]);
        $messages = $messages->all();
        return $messages;
    }

    /* Метод отдает количество непрочитанных сообщений пользователя */
    public static function getMessagesCount() 
    {
        $id = (int)Yii::$app->session->get('user.uid');

        $mess = (new \yii\db\Query())
        ->select('count(id) as cnt')
        ->from('calc_messreport mr')
        ->where('send=:send and user=:user and ok=:ok', [':send'=>1, ':user'=>(int)$id, ':ok'=> 0])
        ->one();

        return (!empty($mess)) ? $mess['cnt'] : 0;
    }

    public static function getMessagesReadStatus($ids = NULL, $type = NULL)
    {
        $result = (new \yii\db\Query())
        ->select(['num' => 'count(mr.id)', 'id' => 'm.id'])
        ->from(['mr' => 'calc_messreport'])
        ->innerJoin(['m' => 'calc_message'], 'm.id = mr.calc_message')
        ->where(['mr.send' => 1, 'm.send' => 1])
        ->andFilterWhere(['mr.ok' => $type])
        ->andFilterWhere(['in', 'm.id', $ids])
        ->groupBy(['m.id'])
        ->all();
        return $result;
    }

    /* Метод отдает информацию по последнему непрочитанному сообщению */
    public static function getLastUnreadMessage() 
    {
        $id = (int)Yii::$app->session->get('user.uid');
        $mess = [];

        $message = (new \yii\db\Query())
        ->select('m.id as mid, mr.id as mrid, m.name as mtitle, m.files as mfile, m.description as mtext, u.name as employee, s.name as student, m.calc_messwhomtype as group_id, mwt.name as group_name')
        ->from('calc_messreport mr')
        ->leftjoin('calc_message m', 'm.id=mr.calc_message')
        ->leftjoin('user u', 'u.id=m.user')
        ->leftjoin('calc_studname s', 's.id=m.user')
        ->leftjoin('calc_messwhomtype mwt', 'mwt.id=m.calc_messwhomtype')
        ->where('mr.send=:send and mr.user=:user and mr.ok=:ok', [':send'=>1, ':user'=>$id, ':ok'=> 0])
        ->one();

        if(!empty($message)){
            if((int)$message['group_id'] === 100) {
                /* сообщение от студента */
                $sender = $message['student'];
            } elseif((int)$message['group_id'] === 5) {
                /* сообщение от пользователя */
                $sender = $message['employee'];
            } else {
                $sender = $message['group_name'];
            }
            
            if($message['mfile']!=NULL && $message['mfile']!='0'){
                $link = explode('|',$message['mfile']);
                $image = $link[0];
            } else {
                $image = NULL;
            } 

            $mess = [
                'mid' => $message['mid'],
                'rid' => $message['mrid'],
                'sender' => $sender, 
                'title' => $message['mtitle'],
                'body' => $message['mtext'],
                'image' => ($image) ? '/uploads/calc_message/' . $message['mid'] . '/fls/' . $image : NULL,
            ];
        }

        return !empty($mess) ? $mess : null;
    }

    /**
     * Метод проверяет и возвращает id непрочитанного сообщения пользователя
     * @param integer $id
     * @param integer $user
     */
    public static function getUnreadMessageIdByReportId($id, $user) 
    {
        $message = (new \yii\db\Query())
        ->select('id as id')
        ->from('calc_messreport')
        ->where('id=:id and user=:uid and ok=:zero',[':id' => $id,':uid' => $user, ':zero' => 0])
        ->one();

        return $message;
    }

    public static function getMessageDirections($permitted_types = NULL)
    {
        $cmwts = (new \yii\db\Query())
        ->select(['id' => 'id', 'name' => 'name'])
        ->from(['mwt' => 'calc_messwhomtype'])
        ->where(['visible' => 1])
        ->andFilterWhere(['in', 'id', $permitted_types])
        ->orderby(['id' => SORT_ASC])
        ->all();

        return $cmwts;
    }

    public static function getMessageDirectionsArray($permitted_types = NULL)
    {
        $cmwts = static::getMessageDirections($permitted_types);
        $types = [];
        if (is_array($cmwts) && !empty($cmwts)) {
            foreach ($cmwts as $cmwt) {
                if ((int)Yii::$app->session->get('user.ustatus') === 5) {
                    if ((int)$cmwt['id'] === 5 || (int)$cmwt['id'] === 13) {
                        $types[$cmwt['id']] = $cmwt['name'];
                    }
                } else {
                    $types[$cmwt['id']] = $cmwt['name'];
                }
            }
        }
        return $types;
    }

    /**
     * Метод подменяет в строках идентификатор одного студента на идентификатор другого
     */
    public static function changeStudentId($id1, $id2)
    {
        $sql = (new \yii\db\Query())
        ->createCommand()
        ->update(self::tableName(), ['user' => $id1], ['calc_messwhomtype' => 100, 'user' => $id2])
        ->execute();

        return ($sql == 0) ? false : true;
    }
}
