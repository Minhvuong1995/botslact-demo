<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class Channel extends ActiveRecord
{
    
    public function getListChannel(){
        $channel = Channel::find()
            ->orderBy('name')
            ->asArray()
            ->all();
        return $channel; 
    }

    public function addChannel($name,$id_chanel){
       
        $this -> name = $name;
        $this -> id_slack_channel = $id_chanel;
        $result = $this->save();
        return $result; 
    }

    public function delChannel($id){
 
        $channel = Channel::find()
        ->where(['id'=>$id])
        ->one()
        ->delete();
        return $channel; 
    }


}