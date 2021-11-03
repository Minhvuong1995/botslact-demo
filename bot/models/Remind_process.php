<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class Remind_process extends ActiveRecord
{
    
    public function getLstRemindprocess($ts,$idchannel){
        $remind = Remind_process::find()
            ->where(['ts' => $ts])
            ->andwhere(['id_channel' => $idchannel])
            ->asArray()
            ->count(); 
        return $remind;
    }

    public function AddRemindprocess($ts,$idchannel,$timesend){
        $remind = new Remind_process();
        $remind['ts'] = $ts;
        $remind['id_channel'] = $idchannel;
        $remind['time_send'] = $timesend;
        $remind['remind'] = 0;
        $result = $remind ->save();
        return $result; 
    }

    public function getLstRemindprocess_remind(){
        $remind = Remind_process::find()
            ->where(['remind' => 0])
            ->andwhere(['<=','time_send',time()])
            ->asArray()
            ->all(); 
        return $remind; 
    }
    public function getLstRemindprocess_Maxts($idchannel){
        $max = Remind_process::find()
        ->where(['id_channel' => $idchannel])
        ->max('ts');
        
        return $max; 
    }

    public function updateLstRemindprocess($id){
        $remind = Remind_process::findOne($id);
        $remind ->remind = '1';
        $result = $remind->save();
        return $result;
    }

    

}