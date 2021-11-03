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
    const token = "xxxxxxxxxxxxxx";

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
        if(isset($araydata['channels'])){
            foreach ($araydata["channels"] as $value){
                $arr_chanel[$value["id"]] = $value["name"];
            }
        }
        return ($arr_chanel);
    }

    /**
     *  get list bot is set.
     *
     * @return araydata
     */
    public function actionGet()
    {
        if(Yii::$app->request->post())
        {
	   $id ='';
            $bot = new Bot_info();

            $id  = Yii::$app->request->post('id');
            $Bot = $bot->getLstBotByIDChannel($id);
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
        if(Yii::$app->request->get())
        {
            $id = Yii::$app->request->get('id');
            if($id){
		    $bot = new Bot_info();
                $Bot = $bot -> getLstBotByID($id);
                
                return $this->render('edit', [
                'info_bot' => $Bot[0],
                'channel'  => $channel,
                'result'   => true,
                ]);
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
        unset($data['_csrf']);

        if(isset($data_post['id_bot']) &&strlen($data_post['id_bot'])>0){
            $bot = Bot_info::findOne($data_post['id_bot']);
            foreach ($data as $key => $value){
                if(strlen($value)>0){
                    $bot->$key = $value;
                }
            }
            $bot->save();
            $data['id_bot'] = $data_post['id_bot'];
            return $this->render('edit', [
                'channel'  => $channel,
                'info_bot' => $data,
                'result'   => true,
                'save'     =>true,
                ]);
        }
        $bot = new Bot_info();
        foreach ($data as $key => $value){
            if(!empty($value)){
            $bot->$key = $value;
            }
        }
        $resurl = $bot->save();
        $new_id = $bot->getPrimaryKey();
        $bot = new Bot_info();
        $bot_new = $bot -> getLstBotByID($new_id);
        return $this->render('edit', [
            'channel'  => $channel,
            'info_bot' => $bot_new[0],
            'result'   => true,
            'save'     =>true,
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
        $bot = new Bot_info();
        $result = $bot ->del($id);
        return json_encode($result);
    }
}