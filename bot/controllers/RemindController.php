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
        } else {
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
        $remind_chanel = new RemindChannel();
        $list_chanel = $this->getListChannel();
        if (Yii::$app->request->get()) {
            $id = Yii::$app->request->get('id');
            if ($id) {
                $remind_chanel = new RemindChannel();
                $remind_info = $remind_chanel->getRemindChannelById($id);
                return $this->render(
                    'edit',
                    [
                        'info_config' => $remind_info,
                        'list_channel'  => $list_chanel,
                        'result'   => true,
                    ]
                );
            }
        }
        $remind_exits = $remind_chanel->getListRemindChannel();
        foreach ($remind_exits as $config) {
            unset($list_chanel[$config['id_channel']]);
        }
        return $this->render('edit', [
            'list_channel' => $list_chanel,
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

    /**
     * save config 
     *
     */
    public function actionSave()
    {
        $data_post = Yii::$app->request->post();
        $list_chanel = $this->getListChannel();
        $remind_config['check_notify_user'] = '';
        $remind_config['check_notify_channel'] = '';
        $remind_config['name'] = $data_post['name'];
        $remind_config['time_remind'] = $data_post['time_remind'];
        $remind_config['text_remind_private'] = $data_post['text_remind_private'];
        $remind_config['text_remind_group'] = $data_post['text_remind_group'];
        if (isset($data_post['send_private'])) {
            $remind_config['send_private'] = $data_post['send_private'];
        }
        if (isset($data_post['send_group'])) {
            $remind_config['send_group'] = $data_post['send_group'];
        }
        if (isset($data_post['check_notify_user'])) {
            $remind_config['check_notify_user'] = $data_post['check_notify_user'];
        }
        if (isset($data_post['check_notify_channel'])) {
            $remind_config['check_notify_channel'] = $data_post['check_notify_channel'];
        }
        //insert
        if (strlen($data_post['id']) > 0) {
            $config = RemindChannel::findOne($data_post['id']);
            foreach ($remind_config as $key => $config_value) {
                if ($config_value == '') {
                    $config_value = NULL;
                }
                $config->$key = $config_value;
            }
            $config->save();
            $remind_chanel = new RemindChannel();
            $remind_info = $remind_chanel->getRemindChannelByChannelId($data_post['id']);
            return $this->render(
                'edit',
                [
                    'info_config' => $remind_info,
                    'list_channel'  => $list_chanel,
                    'result'   => true,
                    'save'  => true,
                ]
            );
        }
        $remind_config['id_channel'] = $data_post['id_channel'];
        //new
        return $this->addNewRemind($remind_config);
    }

    /**
     *  new config detail.
     *
     * @return json
     */
    private function addNewRemind($NewRemind)
    {
        $config = new RemindChannel();
        foreach ($NewRemind as $key => $remind_details) {
            if (!empty($remind_details)) {
                $config->$key = $remind_details;
            }
        }
        $result = $config->save();
        $new_id = $config->getPrimaryKey();
        $remind = new RemindChannel();
        $remind_new = $remind->getRemindChannelById($new_id);
        $list_chanel = $this->getListChannel();
        return $this->render('edit', [
            'list_channel'  => $list_chanel,
            'info_config' => $remind_new,
            'result'   => $result,
            'save'     => true,
        ]);
    }

    /**
     *  delete config detail.
     *
     * @return json
     */
    public function actionDelete()
    {
        $data_post = Yii::$app->request->post();
        $id = $data_post['id'];
        $remind = new RemindChannel();
        $result = $remind->deleteRemindChannelById($id);
        return true;
    }
}
