<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class Remind_channel extends ActiveRecord
{
    
    public function getLstRemindChannel(){
        $Bot = Remind_channel::find()
            ->orderBy('name_channel')
            ->asArray()
            ->all();
        return $Bot; 
    }

}