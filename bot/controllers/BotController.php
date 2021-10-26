<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Bot_info;
class BotController extends BaseController
{
    const token = "xoxb-2523231391122-2604981139527-fmMzrsfnSpXdGi2s8cVeyg0v";

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $info_chanel = $this->Get_listChannel();
        return $this->render('index', [
            'info_chanel' => $info_chanel,
        ]);
    }

    /**
     *  get mes from slack.
     *
     * @return araydata
     */
    public function actionGetms()
    {
        $token = $this::token;
        $url = "https://slack.com/api/conversations.list";
        $headers[0] = 'Authorization: Bearer ' . $token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($ch);
        curl_close($ch);
        $araydata = [];
        $araydata = json_decode($response,16);
        $arr_chanel = [];
        foreach ($araydata["channels"] as $value){
            $arr_chanel[$value["id"]] = $value["name"];
        }
        var_dump ($arr_chanel);
        die;
        return;
    }
    /**
     * Displays about page.
     *
     * @return array
     */
    private function Get_listChannel()
    {
        $token = $this::token;
        $url = "https://slack.com/api/conversations.list";
        $headers[0] = 'Authorization: Bearer ' . $token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($ch);
        curl_close($ch);
        $araydata = [];
        $araydata = json_decode($response,16);
        $arr_chanel = [];
        foreach ($araydata["channels"] as $value){
            $arr_chanel[$value["id"]] = $value["name"];
        }
        return ($arr_chanel);
    }

    /**
     *  get list bot is set.
     *
     * @return araydata
     */
    public function actionGetbots()
    {
        if(Yii::$app->request->post())
        {
            $id = Yii::$app->request->post('id');
            $Bot = Bot_info::getLstBotByIDChannel($id);
            return json_encode($Bot);
        }
        return;
    }

    /**
     *  change bot detail.
     *
     * @return change page
     */
    public function actionEditbots()
    {
        $channel = $this->Get_listChannel();
        if(Yii::$app->request->get())
        {
            $id = Yii::$app->request->get('id');
            if($id){
                $Bot = Bot_info::getLstBotByID($id);
                
                return $this->render('editbots', [
                'info_bot' => $Bot[0],
                'channel'  => $channel,
                'result'   => true,
                ]);
            }
        }
        return $this->render('editbots', [
            'channel'  => $channel,
            ]);
    }

    /**
     *  save bot detail.
     *
     * @return change page
     */
    public function actionSavebot()
    {
        $channel = $this->Get_listChannel();
        $data = Yii::$app->request->post();
        unset($data['_csrf']);
        if(isset($data['id_bot']) &&strlen($data['id_bot'])>0){
            $bot = Bot_info::findOne($data['id_bot']);
            unset($data['id_bot']);
            foreach ($data as $key => $value){
                if(strlen($value)>0){
                    $bot->$key = $value;
                }
            }
            $bot->save();
            return $this->render('editbots', [
                'channel'  => $channel,
                'result'   => true,
                ]);
        }
        $bot = new Bot_info();
        foreach ($data as $key => $value){
            if(!empty($value)){
            $bot->$key = $value;
            }
        }
        $bot->save();
        return $this->render('editbots', [
            'channel'  => $channel,

            ]);
       
    }
}