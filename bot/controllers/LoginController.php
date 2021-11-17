<?php

namespace app\controllers;

use Yii;
use app\components\AuthHandler;

class LoginController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $session = Yii::$app->session;
        if (isset($session['login_info'])) {
            //check expires time
            if ($session['login_info']['expires_time'] < time()) {
                return $this->goHome();
            }
        }
        return $this->render('index');
    }



    public function onAuthSuccess($client)
    {
        $result = (new AuthHandler($client))->handle();
        if($result){
            return $this->goHome();
        }
        return $this->render('index');
        
    }

     /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionLogout()
    {
        $session = Yii::$app->session;
        unset($session['login_info']);
        return $this->render('index');
    }
}
