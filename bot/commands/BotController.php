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
use app\models\RemindProcess;
use app\models\Channel;

/**
 *
 * This command is check and send mesages to slack.
 *
 * @author vuongdm
 * @since 2021/11/01
 */
class BotController extends Controller
{

    const urlSendms = "https://slack.com/api/chat.postMessage";//post
    const urlGetMesHis = "https://slack.com/api/conversations.history";//get
    const urlGetChannelMember ="https://slack.com/api/conversations.members";
    const token = "xxx";
    const timerelay = 60*5; // 5 minute
    const linkslack = "https://vnlabcenter.slack.com/archives/";

    /**
     * This action will check list bot and remind in channel .
     * @param none
     * @return int Exit code
     */
    public function actionIndex()
    {   
        $Remind_process = new RemindProcess();
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
            $araydata = [];
            $araydata = json_decode($response,16);
            if($bot['remind']){
                
                if(!$Remind_process->getLstRemindprocess($araydata['ts'],$bot['group_id'])){
                    //Add new process
                    $Remind_process->AddRemindprocess($araydata['ts'],$bot['group_id'],time()+$bot['time_remind'],$bot['text_remind'],1);
                }
            }
            else{
                if(!$Remind_process->getLstRemindprocess($araydata['ts'],$bot['group_id'])){
                    //Add new process no send
                    $Remind_process->AddRemindprocess_nosend($araydata['ts'],$bot['group_id'],time(),NULL,1);
                }
            }
        } 
        $this -> getMes();
        return ExitCode::OK;
    }

    /**
     * Get new mesages in channel and send message remind 
     *
     * @return Change data and call api
     */
    private function getMes()
    {
        $Remind_channel = new RemindChannel();
        $Remind_process = new RemindProcess();
        $lst_channel = $Remind_channel->getLstRemindChannel();
        //check for channel
        foreach ($lst_channel as $value) {
            $max_process = $Remind_process->getLstRemindprocess_Maxts($value['id_channel']);
            $url = $this::urlGetMesHis;
            // If channels data already exist  
            if($max_process){
                $data = [
                    "token" => $this::token,
                    "channel" => $value['id_channel'], //"#mychannel",
                    'oldest' => $max_process,
                ];
            }
            // First get data channel
            else{
                $data = [
                    "token" => $this::token,
                    "channel" => $value['id_channel'], //"#mychannel",
                    'limit' => 1,
                    'ts'=>'latest',
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
            //get data true
            if($araydata['ok'] = true){
                foreach ($araydata["messages"] as $mes){
                    //check process exits 
                    if(!$Remind_process->getLstRemindprocess($mes['ts'],$value['id_channel'])){
                        //Add new process
                        $Remind_process->AddRemindprocess($mes['ts'],$value['id_channel'],time()+$value['time_remind'],$value['text_remind'],0);
                    }
                }
            }
        }
        //check process remind
        $lst_process = $Remind_process->getLstRemindprocess_remind();
        $check_see =[];
        // scan list remind
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
                            //if reactions 
                            $check_see[$idU] = true;
                        }
                        
                    }
                }
                if(isset($mes['messages'][0]['reply_users'])){
                    foreach($mes['messages'][0]['reply_users'] as $idU){
                        //if reply
                        $check_see[$idU] = true;
                    }
                }
            }
            //check all user
            foreach($check_see as $id => $user){
                if(!$user){
                    // if not reactions and reply
                    //send remind
                    $url = $this::urlSendms;
                    $data = [
                    "token" => $this::token,
                    "channel" => $id, //"#id",
                    "text"=> " ".$process['text_remind'] . $this::linkslack . $process['id_channel'] .'/p'. str_replace(".","",$process['ts']),
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
            // flagged message is checked.
            $Remind_process->updateLstRemindprocess($process['id']);
        }
    }

    /**
     * This function will get list new messages in channel .
     * @param  string id_channel the id channel 
     * @param  string ts the latest time(á»‰nt) message in channel
     * @return array List mesages
     */
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

        /**
     * This function will get all info users in channel .
     * @param  string id_channel the id channel 
     * @return array List info user
     */
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
