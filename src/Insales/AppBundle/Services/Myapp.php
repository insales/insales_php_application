<?php

namespace Insales\AppBundle\Services;

use Insales\AppBundle\Entity\Account;
use Doctrine\ORM\EntityManager;

/*
 * сервис для установки приложения
 */

class Myapp
{

    protected $em;
    protected $secret;

    public function __construct(EntityManager $entityManager, $api_secret)
    {
        $this->em = $entityManager;
        $this->secret = $api_secret;
    }

    public function prepareShop($shop)
    {
        return strtolower($shop);
    }

    public static function generatePassword($secret, $token)
    {
        return md5("$token$secret");
    }

    public function install($shop, $token, $insales_id)
    {
        $shop = self::prepareShop($shop);
        $account = $this->em->getRepository('InsalesAppBundle:Account')->findOneBy(array('insales_subdomain' => $shop));
        if ($account) {
            return true;
        } else {
            $account = new Account();
            $account->setInsalesSubdomain( $shop );
            $account->setPassword( $this->generatePassword($this->secret, $token) );
            $account->setInsalesId( $insales_id );

            $this->em->persist($account);
            $this->em->flush();
            if ($account->getInsalesId()) {
              return true;
            }
        }
        return false;
    }

    public function uninstall($shop, $password)
    {
        $shop = self::prepareShop($shop);
        $account = $this->em->getRepository('InsalesAppBundle:Account')->findOneBy(array('insales_subdomain' => $shop));
        if (!$account)
        {
            return true;
        }
        if ($account->getPassword() != $password)
        {
            return false;
        }
        $this->em->remove($account);
        $this->em->flush();
        return true;
    }

}
