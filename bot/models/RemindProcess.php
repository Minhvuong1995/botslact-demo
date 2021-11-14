<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class RemindProcess extends ActiveRecord
{

    public function getListRemindProcess($ts, $id_channel)
    {
        $remind = Remind_process::find()
            ->where(['ts' => $ts])
            ->andWhere(['id_channel' => $id_channel])
            ->asArray()
            ->count();
        return $remind;
    }

    public function addRemindProcess($ts, $id_channel, $time_send, $text_remind, $is_bot = 0)
    {
        $remind = new Remind_process();
        $remind['ts'] = $ts;
        $remind['id_channel'] = $id_channel;
        $remind['time_send'] = $time_send;
        $remind['remind'] = 0;
        $remind['text_remind'] = $text_remind;
        $remind['is_bot'] = $is_bot;
        $result = $remind->save();
        return $result;
    }

    public function addRemindProcessNoSend($ts, $id_channel, $time_send, $text_remind, $is_bot = 0)
    {
        $remind = new Remind_process();
        $remind['ts'] = $ts;
        $remind['id_channel'] = $id_channel;
        $remind['time_send'] = $time_send;
        $remind['remind'] = 1;
        $remind['text_remind'] = $text_remind;
        $remind['is_bot'] = $is_bot;
        $result = $remind->save();
        return $result;
    }

    public function getListRemindProcess_remind()
    {
        $remind = Remind_process::find()
            ->where(['remind' => 0])
            ->andWhere(['<=', 'time_send', time()])
            ->asArray()
            ->all();
        return $remind;
    }
    public function getListRemindProcess_Maxts($idchannel)
    {
        $max = Remind_process::find()
            ->where(['id_channel' => $idchannel])
            ->andWhere(['is_bot' => 0])
            ->asArray()
            ->max('ts');

        return $max;
    }

    public function updateListRemindProcess($id)
    {
        $remind = Remind_process::findOne($id);
        $remind->remind = '1';
        $result = $remind->save();
        return $result;
    }
}
