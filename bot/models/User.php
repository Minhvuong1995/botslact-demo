<?php

namespace app\models;

use yii\db\ActiveRecord;

class User extends ActiveRecord
{

    public function getUserByAccountId($Account)
    {
        $user = User::find()
            ->where(['id_account_google' => $Account])
            ->asArray()
            ->one();
        return $user;
    }

    public function insertUser($User)
    {
        $user = new User();
        $user -> id_account_google = $User['id_account'];
        $user -> email = $User['email'];
        $user -> name = NULL;
        $user -> create_date = date("Y-m-d h:i:s");
        $user -> last_login = date("Y-m-d h:i:s");
        $user ->save();
    }


}
