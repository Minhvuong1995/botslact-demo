<?php


namespace app\controllers;

use Yii;
use app\models\RemindChannel;
use app\models\Channel;

class RemindController extends BaseController
{
    const TOKEN = "xxxx";

     /**
     * init
     */
    public function beforeAction($action)
    {
        //check login
        $session = Yii::$app->session;
        if (isset($session['login_info'])) {
            //check expires time
            if ($session['login_info']['expires_time'] < time()) {
                unset($session['login_info']);
                $session['login_error'] = 'Login required';
                return $this->redirect(['login/index']);
            }
        }else {
            $session['login_error'] = 'Login required';
            return $this->redirect(['login/index']);
        }
        return parent::beforeAction($action);
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $remind_chanel = new RemindChannel();
        $list_remind_channel = $remind_chanel->getListRemindChannel();
        return $this->render('index', [
            'info_remind' => $list_remind_channel,
        ]);
    }

        /**
     * Displays edit page.
     *
     * @return string
     */
    public function actionEdit()
    {
        $list_chanel = $this->getListChannel();
        return $this->render('edit', [
            'list_chanel' => $list_chanel,
        ]);
    }

    /**
     * Get list channel in slack.
     *
     * @return array
     */
    private function getListChannel()
    {
        $url = "https://slack.com/api/conversations.list";
        $data = [
            "token" => $this::TOKEN,
            'types' => 'public_channel, private_channel, mpim, im',
            "type" => "channel_shared",
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        curl_close($ch);
        $array_data = [];
        $array_data = json_decode($response, 16);
        $arr_chanel = [];
        if (isset($array_data['channels'])) {
            foreach ($array_data["channels"] as $value) {
                if (isset($value["name"]))
                    $arr_chanel[$value["id"]] = $value["name"];
            }
        }
        //add channel private in local 
        $channel = new Channel();
        $list_local_channel = $channel->getListChannel();
        foreach ($list_local_channel as $channel) {
            $arr_chanel[$value['id_slack_channel']] = $value['name'];
        }
        return ($arr_chanel);
    }
    
}
