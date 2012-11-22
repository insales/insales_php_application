<?php

namespace Insales\AppBundle\Controller;

use Insales\AppBundle\Entity\Account;
use Insales\AppBundle\Lib\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/*
 * Родительский контроллер для контроллеров использующих аутентификацию
 */

class BaseController extends Controller
{
    // возвращает редиректы для аутентификации если она необходима
    // возвращает false если пользователь уже аутентифицирован
    protected function authentication()
    {
        if ( $this->isEnterFromDifferentShop() )
        {
            $this->logout();
        }

        if ( $this->currentApp() && $this->currentApp()->isAuthorized() )
        {
            $account = $this->getDoctrine()->getRepository('InsalesAppBundle:Account')
              ->findOneBy(array('insales_subdomain' => $this->currentApp()->getShop()));
            if ($account) {
                return false; // аутентификация прошла успешно
            }
        }

        $this->storeLocation();

        if ($this->accountByParams())
        {
            return $this->initAuthorization($this->accountByParams());
        } else {
            return $this->redirect($this->generateUrl('_login'));
        }
    }

    protected function accountByParams()
    {
        $request    = $this->getRequest();
        $insales_id = $request->query->get('insales_id');
        $shop       = $request->request->get('shop');

        if ( strlen($insales_id) != 0 )
        {
            $account = $this->getDoctrine()
              ->getRepository('InsalesAppBundle:Account')
              ->findOneBy(array('insales_id' => $insales_id));
        } else {
            $account = $this->getDoctrine()
              ->getRepository('InsalesAppBundle:Account')
              ->findOneBy(array('insales_subdomain' => $shop));
        }

        return $account ? $account : false;
    }

    // формирует url для аутентификации в inSales и возвращает редирект на него
    protected function initAuthorization($account) {
        $session = $this->getRequest()->getSession();
        $session
          ->set('app',
                  new Application(
                    $account->getInsalesSubdomain(),
                    $account->getPassword(),
                    $this->container->getParameter('api_secret'),
                    $this->container->getParameter('api_key'),
                    $this->container->getParameter('api_host'),
                    $this->container->getParameter('api_autologin_path')
                  )
                );
        return $this->redirect($session->get('app')->authorizationUrl());
    }

    protected function isEnterFromDifferentShop()
    {
        $request = $this->getRequest();
        $this->currentApp() and $request->request->get('shop') && $request->request->get('shop') != $this->currentApp()->getShop();
    }

    protected function currentApp()
    {
        $session = $this->getRequest()->getSession();
        return $session->get('app');
    }

    protected function storeLocation($path = false)
    {
        if (!$path)
        {
            $request = $this->getRequest();
            $path = $request->getPathInfo();
        }
        $session = $this->getRequest()->getSession();
        $session->set('return_to', $path);
    }

    protected function location()
    {
        $session = $this->getRequest()->getSession();
        return $session->get('return_to');
    }

    protected function logout()
    {
        $this->getRequest()->getSession()->clear();
    }

}
