<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\Bot_info;
class BotcheckController extends BaseController
{
    const urlSendms = "https://hooks.slack.com/services/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";//post
    const urlGetMesHis = "https://slack.com/api/conversations.history";//get
    const urlGetChannelMember ="https://slack.com/api/conversations.members";
    const token = "xoxb-2523231391122-2604981139527-fmMzrsfnSpXdGi2s8cVeyg0v";
    
    /**
     * index.
     *
     * @return action
     */
    public function actionIndex()
    {
        $settime = date_default_timezone_set ('Asia/Saigon');
        $date = date("Y-m-d");
        $a = date("w");
        $day_of_week =$a+1;
        $time = date("H:i:s");
        $day_of_month = sprintf("%02d", date("d"));
        $month =  sprintf("%02d", date("m"));
        $Sendlist = Bot_info::getLstBotSend();
        $useragent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";

        foreach ($Sendlist as $bot){
            $ch = curl_init();
            $payload = 'payload={"channel": "#notification", "bot": "webhookbot", "text": "'.$bot['content'].'", "icon_emoji": ":ghost:"}';
            curl_setopt($ch, CURLOPT_USERAGENT, $useragent); //set our user agent
            curl_setopt($ch, CURLOPT_POST, TRUE); //set how many paramaters to post
            curl_setopt($ch, CURLOPT_URL,$this::urlSendms); //set the url we want to use
            curl_setopt($ch, CURLOPT_POSTFIELDS,$payload); 
            
            curl_exec($ch); //execute and get the results
            curl_close($ch);
        } 
        echo "<pre>";
         var_dump($time);die;
        echo "</pre>";
        return $this->render('index');
    }

    /**
     * .
     *
     * @return .
     */
    public function actionGetmes()
    {
        $url = $this::urlGetMesHis;
        $data = [
            "token" => $this::token,
            "channel" => 'C02J36FSC1Z', //"#mychannel",
            "limit"=> 10,
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
         curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        
        $response = curl_exec($ch);
        curl_close($ch);
        $araydata = [];
        $araydata = json_decode($response,16);
        echo "<pre>";
         var_dump($araydata);die;
        echo "</pre>";
        return $this->render('index');
    }



    public function actionGetallmember()
    {
        $url = $this::urlGetChannelMember;
        $data = [
            "token" => $this::token,
            "channel" => 'C02FKD9A7MJ', //"#mychannel",
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        
        $response = curl_exec($ch);
        curl_close($ch);
        $araydata = [];
        $araydata = json_decode($response,16);
        echo "<pre>";
         var_dump($araydata['members']);die;
        echo "</pre>";
        return $this->render('index');
    }



    

}