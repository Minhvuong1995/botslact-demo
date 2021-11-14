<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\models\BotInfo;
use app\models\RemindChannel;
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
        $remind_process = new RemindProcess();
        $bot = new BotInfo();
        date_default_timezone_set('Asia/Saigon');
        $send_list = $bot->getListBotSend();
        foreach ($send_list as $bot) {
            $url = $this::URL_POST_MESSAGE;
            $data = [
                "token" => $this::TOKEN,
                "channel" => $bot['group_id'], //"#myChannel",
                "text" => $bot['content'],
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $response = curl_exec($ch);
            curl_close($ch);
            $array_data = [];
            $array_data = json_decode($response, 16);
            if ($bot['remind']) {
                if (!$remind_process->getListRemindProcess($array_data['ts'], $bot['group_id'])) {
                    //Add new process
                    $remind_process->addRemindProcessBot($array_data['ts'], $bot['group_id'], time() + $bot['time_remind'], $bot['text_remind'], 1);
                }
            } else {
                if (!$remind_process->getListRemindProcess($array_data['ts'], $bot['group_id'])) {
                    //Add new process no send
                    $remind_process->addRemindProcessNoSend($array_data['ts'], $bot['group_id'], time(), NULL, 1);
                }
            }
        }
        $this->getMes();
        $this->checkProcessSendMessages();
        return ExitCode::OK;
    }

    /**
     * Get new mesages in channel and send message remind 
     *
     * @return Change data and call api
     */
    public function getMes()
    {
        $remind_channel = new RemindChannel();
        $remind_process = new RemindProcess();
        $list_channel = $remind_channel->getListRemindChannel();
        //check for channel
        foreach ($list_channel as $value) {
            $max_process = $remind_process->getListRemindprocess_Maxts($value['id_channel']);
            $url = $this::URL_GET_MESSAGE_HISTORY;
            // If channels data already exist  
            if ($max_process) {
                $data = [
                    "token" => $this::TOKEN,
                    "channel" => $value['id_channel'], //"#myChannel",
                    'oldest' => $max_process,
                ];
            }
            // First get data channel
            else {
                $data = [
                    "token" => $this::TOKEN,
                    "channel" => $value['id_channel'], //"#myChannel",
                    'limit' => 1,
                    'ts' => 'latest',
                ];
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $response = curl_exec($ch);
            curl_close($ch);
            $array_data = [];
            $array_data = json_decode($response, 16);
            //check response
            if (!$array_data['ok']) {
                //next chanel
                continue;
            }
            // Set process send message
            foreach ($array_data["messages"] as $message) {
                //check process exits 
                if (!$remind_process->getListRemindProcess($message['ts'], $value['id_channel'])) {
                    //Add new process
                    $remind_process->AddRemindProcess($message['ts'], $value['id_channel'], time() + $value['time_remind'], NULL, 0);
                }
            }
        }
    }

    private function checkProcessSendMessages()
    {
        $remind_process = new RemindProcess();
        $remind_channel = new RemindChannel();
        //check process remind
        $list_process = $remind_process->getListRemindProcess_remind();
        // scan list remind
        foreach ($list_process as $process) {
            $check_see = [];
            $flg_notify_channel = 0;
            $flg_notify_user = 0;
            $list_notify=[];
            $member = $this->getAllMember($process['id_channel'])['members'];
            foreach ($member as $mem) {
                $check_see[$mem] = false;
            }
            $message = $this->getSlackMessages($process['id_channel'], $process['ts']);
            // Check response
            if ($message['ok']) {
                if (isset($message['messages'][0]['reactions'])) {
                    foreach ($message['messages'][0]['reactions'] as $reactions) {
                        foreach ($reactions['users'] as $id_user) {
                            //if reactions 
                            $check_see[$id_user] = true;
                        }
                    }
                }
                if (isset($message['messages'][0]['reply_users'])) {
                    foreach ($message['messages'][0]['reply_users'] as $id_user) {
                        //if reply
                        $check_see[$id_user] = true;
                    }
                }
                if (isset($message['messages'][0]['blocks'][0]['elements'])) {
                    foreach ($message['messages'][0]['blocks'][0]['elements'][0]['elements'] as $element) {
                        //if notify channel
                        if ($element['type'] == 'broadcast') {
                            $flg_notify_channel = 1;
                        }
                        //if notify user 
                        if ($element['type'] == 'user') {
                            $flg_notify_user = 1;
                            $list_notify[] = $element['user_id'];
                        }
                    }
                }
            } else {
                //next process
                continue;
            }
            //Send messages
            if ($process['is_bot']) {
                //check all user
                foreach ($check_see as $id => $user) {
                    if (!$user) {
                        // if not reactions and reply
                        //send remind
                        $url = $this::URL_POST_MESSAGE;
                        $data = [
                            "token" => $this::TOKEN,
                            "channel" => $id, //"#id",
                            "text" => " " . $process['text_remind'] . $this::LINK_SLACK_APP . $process['id_channel'] . '/p' . str_replace(".", "", $process['ts']),
                            "as_user" => true,
                        ];
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                        $response = curl_exec($ch);
                        curl_close($ch);
                    }
                }
            } else {
                var_dump( $check_see);
                $list_channel = $remind_channel->getListRemindChannelByChannelId($process['id_channel']);
                if(!$list_channel){
                    $remind_process->updateListRemindProcess($process['id']);
                    continue;
                }
                var_dump( $flg_notify_user);
                //check config notify channel
                if ($list_channel['check_notify_channel']) {
                    $flg_notify_channel++;
                }
                //check config notify user
                if ($list_channel['send_private']) {
                    $flg_notify_user++;
                }
                var_dump( $flg_notify_user);
                //Check notify group or all
                if ((!$list_channel['check_notify_channel'] && !$list_channel['check_notify_user']) || $flg_notify_channel == 2) {
                    //Send all
                    if ($list_channel['send_group'] ==1) {
                        $content = ' ';
                        $content .= $list_channel['text_remind_group'];
                        foreach ($check_see as $id => $value) {
                            // not see
                            if (!$value) {
                                $content .= ' <@' . $id . '> ';
                            }
                        }
                        //send remind to group
                        $url = $this::URL_POST_MESSAGE;
                        $data = [
                            "token" => $this::TOKEN,
                            "channel" => $process['id_channel'], //"#idchannel",
                            "text" => $content . " " . $this::LINK_SLACK_APP . $process['id_channel'] . '/p' . str_replace(".", "", $process['ts']),
                        ];
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                        $response = curl_exec($ch);
                        curl_close($ch);
                    }
                    if ($list_channel['send_private']) {
                        foreach ($check_see as $id => $value) {
                            // not see
                            if (!$value) {
                                //send to user
                                $url = $this::URL_POST_MESSAGE;
                                $data = [
                                    "token" => $this::TOKEN,
                                    "channel" => $id, //"#id",
                                    "text" =>  $list_channel['text_remind_private'] . " " . $this::LINK_SLACK_APP . $process['id_channel'] . '/p' . str_replace(".", "", $process['ts']),
                                    "as_user" => true,
                                ];
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $url);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                                $response = curl_exec($ch);
                                curl_close($ch);
                            }
                        }
                    }
                } else {
                    //notify user
                    if ($flg_notify_user == 2) {
                        //check send group
                        if ($list_channel['send_group']) {
                            $content = ' ';
                            $content .= $list_channel['text_remind_group'];
                            foreach ($list_notify as $user) {
                                if (!$check_see[$user]) {
                                    $content .= ' <@' . $user . '> ';
                                }
                            }
                            //send remind to group
                            $url = $this::URL_POST_MESSAGE;
                            $data = [
                                "token" => $this::TOKEN,
                                "channel" => $process['id_channel'], //"#idchannel",
                                "text" => $content . " " . $this::LINK_SLACK_APP . $process['id_channel'] . '/p' . str_replace(".", "", $process['ts']),
                            ];
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                            $response = curl_exec($ch);
                            curl_close($ch);
                        }
                        //send private
                        if ($list_channel['send_private']) {
                            foreach ($list_notify as $user) {
                                if (!$check_see[$user]) {
                                    //send to user
                                    $url = $this::URL_POST_MESSAGE;
                                    $data = [
                                        "token" => $this::TOKEN,
                                        "channel" => $user, //"#id",
                                        "text" =>  $list_channel['text_remind_private'] . " " . $this::LINK_SLACK_APP . $process['id_channel'] . '/p' . str_replace(".", "", $process['ts']),
                                        "as_user" => true,
                                    ];
                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, $url);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                                    $response = curl_exec($ch);
                                    curl_close($ch);
                                }
                            }
                        }
                    }
                }
            }
            // flagged message is checked.
            $remind_process->updateListRemindProcess($process['id']);
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
