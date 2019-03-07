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
            'destination_type' => 'm.calc_messwhomtype',
            'reciever_id' => 'm.refinement_id',
            'files' => 'm.files',
            'sended' => 'm.send'
        ])
        ->from(['m' => 'calc_message'])
        ->where([
            'm.id' => $id
        ])
        ->one();
        if ($message !== null) {
            $receiver = null;
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
            if ($receiver !== null) {
                $message['receiver'] = $receiver['name'];
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
