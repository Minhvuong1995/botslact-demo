<?php

namespace app\components;

use app\models\Auth;
use app\models\Profile;
use app\models\User;
use Yii;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;


/**
 * AuthHandler handles successful authentication via Yii auth component
 */
class AuthHandler
{
    const DOMAIN = 'vietnamlab.vn';
    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function handle()
    {
        $session = Yii::$app->session;
        $attributes = $this->client->getUserAttributes();
        $access_token= $this->client->getAccessToken();
        $email = ArrayHelper::getValue($attributes, 'email');
        //Check email 
        $domain =  explode('@', $email)[1];
        if($domain != $this::DOMAIN){
            $session['login_error'] = 'Email domain is not valid in this app!';
            return false;
        }
        $login_info = [];
        $login_info['email'] = $email;
        $login_info['id_account'] = ArrayHelper::getValue($attributes, 'id');
        // var_dump($access_token);die;
        $login_info['expires_time'] = time()+ ArrayHelper::getValue($access_token->getParams(), 'expires_in');
        $login_info['token_google_auth'] = ArrayHelper::getValue($access_token->getParams(), 'id_token');
        $session['login_info'] =$login_info;
        $user = new User();
        if(!$user->getUserByAccountId($login_info['id_account']))
        $user -> insertUser($login_info);
        return true;
    }

    
}
