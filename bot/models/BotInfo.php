<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class BotInfo extends ActiveRecord
{
    public static function Bot_info()
    {
        return 'Bot_info';
    }

    public function getListBotByIDChannel($idChannel)
    {
        $Bot = BotInfo::find()
            ->where(['group_id' => $idChannel])
            ->orderBy('name')
            ->asArray()
            ->all();
        return $Bot;
    }

    public function getListBotByID($idChannel)
    {
        $bot = BotInfo::find()
            ->where(['id_bot' => $idChannel])
            ->orderBy('name')
            ->asArray()
            ->all();
        return $bot;
    }
    public function getListBotSend()
    {
        $a = date("w");
        $day_of_week = $a + 1;
        $time = date("H:i");
        $day_of_month = sprintf("%02d", date("d"));
        $month =  sprintf("%02d", date("m"));
        $send_list = BotInfo::find()
            ->where(['time_send' => $time])
            ->andWhere(['or', ['like', 'date_send', '%' . $day_of_month . '%', false], ['date_send' => null]])
            ->andWhere(['or', ['like', 'month_send', '%' . $month . '%', false], ['month_send' => null]])
            ->andWhere(['or', ['like', 'date_of_week', '%' . $day_of_week . '%', false], ['date_of_week' => null]])
            ->orderBy('name')
            ->asArray()
            ->all();
        return $send_list;
    }

    public function del($id)
    {
        $bot = BotInfo::find($id)
            ->one()
            ->delete();
        return $bot;
    }
}
