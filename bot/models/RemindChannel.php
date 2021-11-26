<?php

namespace app\models;

use yii\db\ActiveRecord;

class RemindChannel extends ActiveRecord
{

    public function getListRemindChannel()
    {
        $remind = RemindChannel::find()
            ->orderBy('id')
            ->asArray()
            ->all();
        return $remind;
    }

    public function getRemindChannelByChannelId($id)
    {
        $remind = RemindChannel::find()
            ->where(['id_channel' => $id])
            ->orderBy('id')
            ->asArray()
            ->one();
        return $remind;
    }

    public function getRemindChannelById($id)
    {
        $remind = RemindChannel::find()
            ->where(['id' => $id])
            ->orderBy('id')
            ->asArray()
            ->one();
        return $remind;
    }

    public function deleteRemindChannelById($id)
    {
        $remind = new RemindChannel();
        $result = $remind->find()
            ->where(['id' => $id])
            ->one()
            ->delete();
        return json_encode($result);
    }
}
