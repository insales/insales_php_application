<?php

namespace Insales\AppBundle\Controller;

use Insales\AppBundle\Services\Myapp;
use Insales\AppBundle\Lib\Application;

/*
 * Контроллер отвечает за аутентификацию
 */

class SessionController extends BaseController
{
    public function loginAction()
    {
        return $this->render('InsalesAppBundle:Session:login.html.twig');
    }

    public function processloginAction()
    {
        $account = parent::accountByParams();
        if ($account)
        {
            return parent::initAuthorization($account);
        } else {
            $this->get('session')->getFlashBag()->add('notice', 'Магазин не найден.');
            return $this->render('InsalesAppBundle:Session:login.html.twig');
        }
    }

    public function autologinAction()
    {
        $request    = $this->getRequest();
        $token = $request->query->get('token');
        if ( parent::currentApp() && parent::currentApp()->authorize($token) )
        {
            if (parent::location())
            {
                return $this->redirect(parent::location());
            } else {
                return $this->redirect($this->generateUrl('_main_page'));
            }
        } else {
            return $this->redirect($this->generateUrl('_login'));
        }
    }

    public function logoutAction()
    {
        // если метод возвращает редирект, значит пользователь нe аутентифицирован
        // и мы запускаем процесс аутентификации
        if (parent::authentication())
        {
            return parent::authentication();
        }
        parent::logout();
        return $this->redirect($this->generateUrl('_login'));
    }
}
