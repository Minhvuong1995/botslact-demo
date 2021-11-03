<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\Bot_info;
use app\models\Remind_channel;
use app\models\Remind_process;
class BotcheckController extends BaseController
{
    const urlSendms = "https://slack.com/api/chat.postMessage";//post
    const urlGetMesHis = "https://slack.com/api/conversations.history";//get
    const urlGetChannelMember ="https://slack.com/api/conversations.members";
    const token = "xxxxxxxxxxxxxx";
    const timerelay = 60*5; // 5 minute
    const linkslack = "https://vnlabcenter.slack.com/archives/";
    
    /**
     * index.
     *
     * @return action
     */
    public function actionIndex()
    {
        $settime = date_default_timezone_set ('Asia/Saigon');
        $Sendlist = Bot_info::getLstBotSend();
        $useragent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";

        foreach ($Sendlist as $bot){


            $url = $this::urlSendms;
            $data = [
                "token" => $this::token,
                "channel" => $bot['group_id'], //"#mychannel",
                "text"=> $bot['content'],
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
            $response = curl_exec($ch);
            curl_close($ch);


        } 
        $this -> getMes();
        return;
    }

    /**
     * .
     *
     * @return 
     */
    public function getMes()
    {
        $lst_channel = Remind_channel::getLstRemindChannel();
        //check for channel
        foreach($lst_channel as $value){
            $max_process = Remind_process::getLstRemindprocess_Maxts($value['id_channel']);
            $url = $this::urlGetMesHis;
            $data = [
                "token" => $this::token,
                "channel" => $value['id_channel'], //"#mychannel",
                'oldest' => $max_process,
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
            $response = curl_exec($ch);
            curl_close($ch);
            $araydata = [];
            $araydata = json_decode($response,16);

            if($araydata['ok'] = true){
                foreach ($araydata["messages"] as $mes){
                    //check process exits 
                    if(!Remind_process::getLstRemindprocess($mes['ts'],$value['id_channel'])){
                        //Add new process
                        Remind_process::AddRemindprocess($mes['ts'],$value['id_channel'],time()+$this::timerelay);
                    }
                }
            }
        }
        //check process remind
        $lstproces = Remind_process::getLstRemindprocess_remind();
        $checksee =[];

        foreach($lstproces as $process){
            $member = $this->getAllMember($process['id_channel'])['members'];
            foreach($member as $mem){
                $checksee[$mem] = false;
            }
            $mes = $this->getSlackMess($process['id_channel'],$process['ts']);
            if($mes['ok']){
                if(isset($mes['messages'][0]['reactions'])){
                    foreach($mes['messages'][0]['reactions'] as $react){
                        foreach ($react['users'] as $idU){
                            $checksee[$idU] = true;
                        }
                        
                    }
                }
                if(isset($mes['messages'][0]['reply_users'])){
                    foreach($mes['messages'][0]['reply_users'] as $idU){
                        $checksee[$idU] = true;
                    }
                }
            }
            foreach($checksee as $id => $user){
                if(!$user){
                    $url = $this::urlSendms;
                    $data = [
                    "token" => $this::token,
                    "channel" => $id, //"#id",
                    "text"=> "Đừng bỏ lỡ thông tin quan trọng nhé :)). " . $this::linkslack . $process['id_channel'] .'/p'. str_replace(".","",$process['ts']),
                    "as_user" => true,
                    ];
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    
                }
            }
            Remind_process::updateLstRemindprocess($process['id']);
        }
    }

    private function getSlackMess($id_channel,$ts){
        $url = $this::urlGetMesHis;
        $data = [
            "token" => $this::token,
            "channel" => $id_channel, //"#mychannel",
            "latest" => $ts,
            "inclusive"=>"true",
            "limit"=> 1,
            
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        $response = curl_exec($ch);
        curl_close($ch);
        $araydata = [];
        $araydata = json_decode($response,16);
        return $araydata;
    }

    public function getAllMember($id_Channel)
    {
        $url = $this::urlGetChannelMember;
        $data = [
            "token" => $this::token,
            "channel" => $id_Channel, //"#mychannel",
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        
        $response = curl_exec($ch);
        curl_close($ch);
        $araydata = [];
        $araydata = json_decode($response,16);
        return $araydata;
    }



    

}