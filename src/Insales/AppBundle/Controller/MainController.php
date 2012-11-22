<?php

namespace Insales\AppBundle\Controller;

use Insales\AppBundle\Entity\Account;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Insales\AppBundle\Lib\Application;

class MainController extends BaseController
{
    public function indexAction()
    {
        // если метод authentication возвращает редирект - редиректим
        // если false - значит пользователь аутентифицирован - стандартное поведение action'а
        if (parent::authentication()) { return parent::authentication(); }

        return $this->render('InsalesAppBundle:Main:index.html.twig');
    }
}
