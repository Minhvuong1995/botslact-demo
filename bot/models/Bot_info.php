<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class Bot_info extends ActiveRecord
{
    
    public function getLstBotByIDChannel($idChannel){
        $Bot = Bot_info::find()
            ->where(['group_id' => $idChannel])
            ->orderBy('name')
            ->asArray()
            ->all();
        return $Bot; 
    }

    public function getLstBotByID($idChannel){
        $bot = Bot_info::find()
        ->where(['id_bot' => $idChannel])
        ->orderBy('name')
        ->asArray()
        ->all();
        return $bot; 
    }
    public function getLstBotSend(){
        $date = date("Y-m-d");
        $a = date("w");
        $day_of_week =$a+1;
        $time = date("H:i");
        $day_of_month = sprintf("%02d", date("d"));
        $month =  sprintf("%02d", date("m"));
        $sendlist = Bot_info::find()
        ->where(['time_send' => $time])
        ->andwhere(['or',['like', 'date_send','%'. $day_of_month . '%', false],['date_send'=>null]])
        ->andwhere(['or',['like', 'month_send','%'. $month . '%', false],['month_send'=>null]])
        ->andwhere(['or',['like', 'date_of_week','%'. $day_of_week . '%', false],['date_of_week'=>null]])
        ->orderBy('name')
        ->asArray()
        ->all();
        return $sendlist; 
    }

    public function del($id){
        $bot = Bot_info::find($id)
        ->one()
        ->delete();
        return $bot ;
    }

}