<?php

namespace Insales\AppBundle\Controller;

use Insales\AppBundle\Entity\Account;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Контроллер отвечает за установку/удаление приложения
 */

class ApplicationController extends Controller
{
    /**
     * установка приложения по запросу из inSales
     */
    public function installAction()
    {
        $request     = $this->getRequest();

        $shop        = $request->query->get('shop');
        $token       = $request->query->get('token');
        $insales_id  = $request->query->get('insales_id');

        if ( (strlen($shop) == 0) || (strlen($token) == 0) || (strlen($insales_id) == 0) )
        {
            throw new \Exception('Unsufficient arguments');
        }

        $app = $this->get('myapp_service');

        if ( $app->install($shop, $token, $insales_id) )
        {
            return new Response('', 200);
        } else {
            throw new \Exception('Installation failed');
        }
    }

    /**
     * удаление приложения по запросу из inSales
     * пришедший параметр token содержит password
     */
    public function uninstallAction()
    {
        $request     = $this->getRequest();

        $shop        = $request->query->get('shop');
        $token       = $request->query->get('token');

        if ( (strlen($shop) == 0) || (strlen($token) == 0) )
        {
            throw new \Exception('Unsufficient arguments');
        }

        $app = $this->get('myapp_service');

        if ( $app->uninstall($shop, $token) )
        {
            return new Response('', 200);
        } else {
            throw new \Exception('Fail to uninstall');
        }

    }
}
