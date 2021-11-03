<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\models\LoginForm;
use app\models\Bot_info;
use app\models\Remind_channel;
use app\models\Remind_process;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class BotController extends Controller
{

    const urlSendms = "https://slack.com/api/chat.postMessage";//post
    const urlGetMesHis = "https://slack.com/api/conversations.history";//get
    const urlGetChannelMember ="https://slack.com/api/conversations.members";
    const token = " xxxxxxxxxx";
    const timerelay = 60*5; // 5 minute
    const linkslack = "https://vnlabcenter.slack.com/archives/";

    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex($message = 'im bot')
    {   
        $bot = new Bot_info();
        $settime = date_default_timezone_set ('Asia/Saigon');
        $Sendlist = $bot->getLstBotSend();
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
        return ExitCode::OK;
    }

    /**
     * .
     *
     * @return 
     */
    private function getMes()
    {
        $Remind_channel = new Remind_channel();
        $Remind_process = new Remind_process();
        $lst_channel = $Remind_channel->getLstRemindChannel();
        //check for channel
        foreach($lst_channel as $value){
            $max_process = $Remind_process->getLstRemindprocess_Maxts($value['id_channel']);
            $url = $this::urlGetMesHis;
            if($max_process){
                $data = [
                    "token" => $this::token,
                    "channel" => $value['id_channel'], //"#mychannel",
                    'oldest' => $max_process,
                ];
            }
            else{
                $data = [
                    "token" => $this::token,
                    "channel" => $value['id_channel'], //"#mychannel",
                    'latest' => true,
                    'limit' => 1,
                ];
            }
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
                    if(!$Remind_process->getLstRemindprocess($mes['ts'],$value['id_channel'])){
                        //Add new process
                        $Remind_process->AddRemindprocess($mes['ts'],$value['id_channel'],time()+$this::timerelay);
                    }
                }
            }
        }
        //check process remind
        $lst_process = $Remind_process->getLstRemindprocess_remind();
        $check_see =[];

        foreach($lst_process as $process){
            $member = $this->getAllMember($process['id_channel'])['members'];
            foreach($member as $mem){
                $check_see[$mem] = false;
            }
            $mes = $this->getSlackMes($process['id_channel'],$process['ts']);
            if($mes['ok']){
                if(isset($mes['messages'][0]['reactions'])){
                    foreach($mes['messages'][0]['reactions'] as $react){
                        foreach ($react['users'] as $idU){
                            $check_see[$idU] = true;
                        }
                        
                    }
                }
                if(isset($mes['messages'][0]['reply_users'])){
                    foreach($mes['messages'][0]['reply_users'] as $idU){
                        $check_see[$idU] = true;
                    }
                }
            }
            foreach($check_see as $id => $user){
                if(!$user){
                    $url = $this::urlSendms;
                    $data = [
                    "token" => $this::token,
                    "channel" => $id, //"#id",
                    "text"=> "Don't miss important news :)). " . $this::linkslack . $process['id_channel'] .'/p'. str_replace(".","",$process['ts']),
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
            $Remind_process->updateLstRemindprocess($process['id']);
        }
    }

    private function getSlackMes($id_channel,$ts){
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

    private function getAllMember($id_Channel)
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
