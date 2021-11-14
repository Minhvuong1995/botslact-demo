<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class RemindChannel extends ActiveRecord
{
    
    public function getListRemindChannel(){
        $remind = RemindChannel::find()
            ->orderBy('id')
            ->asArray()
            ->all();
        return $remind; 
    }

    public function getListRemindChannelByChannelId($id_channel){
        $remind = RemindChannel::find()
            ->where(['id_channel' => $id_channel])
            ->orderBy('id')
            ->asArray()
            ->one();
        return $remind; 
    }


}