<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\BotInfo;
use app\models\Channel;

class BotController extends BaseController
{
    const TOKEN = "xxxx";

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $info_chanel = $this->getListChannel();
        return $this->render('index', [
            'info_chanel' => $info_chanel,
        ]);
    }


    /**
     * Displays about page.
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
        $channel = new Channel();
        $local_channel = $channel->getListChannel();
        foreach ($local_channel as $value) {
            $arr_chanel[$value['id_slack_channel']] = $value['name'];
        }
        return ($arr_chanel);
    }

    /**
     *  get list bot is set.
     *
     * @return arrayData
     */
    public function actionGet()
    {
        if (Yii::$app->request->post()) {
            $id = '';
            $bot = new BotInfo();

            $id  = Yii::$app->request->post('id');
            $Bot = $bot->getListBotByIDChannel($id);
            return json_encode($Bot);
        }
        return;
    }

    /**
     *  change bot detail.
     *
     * @return change page
     */
    public function actionEdit()
    {
        $channel = $this->getListChannel();
        if (Yii::$app->request->get()) {
            $id = Yii::$app->request->get('id');
            if ($id) {
                $bot = new BotInfo();
                $Bot = $bot->getListBotByID($id);

                return $this->render(
                    'edit',
                    [
                        'info_bot' => $Bot[0],
                        'channel'  => $channel,
                        'result'   => true,
                    ]
                );
            }
        }
        return $this->render('edit', [
            'channel'  => $channel,
        ]);
    }

    /**
     *  save bot detail.
     *
     * @return change page
     */
    public function actionSave()
    {
        $channel = $this->getListChannel();
        $data_post = Yii::$app->request->post();
        $data['name'] = $data_post['name'];
        $data['group_id'] = $data_post['group_id'];
        $data['content'] = $data_post['content'];
        $data['time_send'] = $data_post['time_send'];
        $data['date_send'] = $data_post['date_send'];
        $data['month_send'] = $data_post['month_send'];
        $data['date_of_week'] = $data_post['date_of_week'];
        if (isset($data_post['remind'])) {
            $data['remind'] = $data_post['remind'];
            $data['time_remind'] = $data_post['time_remind'];
            $data['text_remind'] = $data_post['text_remind'];
        } else {
            $data['remind'] = 0;
            $data['time_remind'] = 0;
            $data['text_remind'] = NULL;
        }

        if (isset($data_post['id_bot']) && strlen($data_post['id_bot']) > 0) {
            $bot = BotInfo::findOne($data_post['id_bot']);

            foreach ($data as $key => $value) {
                if ($value == '') {
                    $value = NULL;
                }
                $bot->$key = $value;
            }
            $bot->save();
            $data['id_bot'] = $data_post['id_bot'];
            return $this->render('edit', [
                'channel'  => $channel,
                'info_bot' => $data,
                'result'   => true,
                'save'     => true,
            ]);
        }
        $bot = new BotInfo();
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                $bot->$key = $value;
            }
        }
        $result = $bot->save();
        $new_id = $bot->getPrimaryKey();
        $bot = new BotInfo();
        $bot_new = $bot->getListBotByID($new_id);
        return $this->render('edit', [
            'channel'  => $channel,
            'info_bot' => $bot_new[0],
            'result'   => $result,
            'save'     => true,
        ]);
    }

    /**
     *  delete bot detail.
     *
     * @return json
     */
    public function actionDelete()
    {
        $data_post = Yii::$app->request->post();
        $id = $data_post['id'];
        $bot = new BotInfo();
        $result = $bot->find()
            ->where(['id_bot' => $id])
            ->one()
            ->delete();
        return json_encode($result);
    }

    /**
     *  channel.
     *
     * @return view
     */
    public function actionChannel()
    {
        if (Yii::$app->request->post()) {
            $data_post = Yii::$app->request->post();
            $name = $data_post['name'];
            $id_slack_channel = $data_post['id_slack_channel'];
            $channel = new Channel();
            $channel->addChannel($name, $id_slack_channel);
            $result = $channel->getListChannel();
            return $this->render('channel', [
                'channel'  => $result,
                'save'     => true,
            ]);
        }
        $channel = new Channel();
        $result = $channel->getListChannel();
        return $this->render('channel', [
            'channel'  => $result,
        ]);
    }


    /**
     *  channel add.
     *
     * @return json
     */
    public function actionDel()
    {
        $data_post = Yii::$app->request->post();
        $id = $data_post['id'];
        $channel = new Channel();
        $channel->delChannel($id);
        $result = $channel->getListChannel();
        return true;
    }
}
