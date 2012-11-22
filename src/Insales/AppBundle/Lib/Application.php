<?php

namespace Insales\AppBundle\Lib;

use Symfony\Component\DependencyInjection\ContainerAware;

use Insales\AppBundle\Services\Myapp;

/*
 * Класс который сохраняется в сессию и хранит данные об авторизации
 */
class Application
{
    protected $authorized;
    protected $shop;
    protected $password;

    protected $api_secret;
    protected $api_key;
    protected $api_host;
    protected $api_autologin_path;

    protected $auth_token;
    protected $salt;

    public function __construct($shop, $password, $api_secret, $api_key, $api_host, $api_autologin_path)
    {
        $this->authorized = false;
        $this->shop     = $shop;
        $this->password = $password;

        $this->api_secret = $api_secret;
        $this->api_key    = $api_key;
        $this->api_host   = $api_host;
        $this->api_autologin_path = $api_autologin_path;
    }

    public function authorizationUrl()
    {
        $this->storeAuthToken();
        return "http://$this->shop/admin/applications/$this->api_key/login?token=$this->salt&login=http://$this->api_host/$this->api_autologin_path";
    }

    public function getShop()
    {
        return $this->shop;
    }

    public function isAuthorized()
    {
        return $this->authorized;
    }

    public function storeAuthToken()
    {
        if (strlen($this->auth_token) == 0)
        {
           $this->auth_token = Myapp::generatePassword($this->password, $this->salt());
        }
        return $this->auth_token;
    }

    public function authorize($token)
    {
      $this->authorized = false;
      if ($this->auth_token && $this->auth_token == $token)
      {
          $this->authorized = true;
      }
      return $this->authorized;
    }

    public function salt()
    {
        if (strlen($this->salt) == 0)
        {
          $this->salt = md5("Twulvyeik".getmypid().time()."thithAwn");
        }
        return $this->salt;
    }

}
