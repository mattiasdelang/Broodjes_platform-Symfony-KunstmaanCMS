<?php

namespace Kuma\BroodjesBundle\Helper\Slack;

use Doctrine\ORM\EntityManager;

class LowCreditService
{
    private $em;
    private $webHook;
    private $token;

    public function __construct(EntityManager $em, WebHookService $webHook, $token)
    {
        $this->em = $em;
        $this->webHook = $webHook;
        $this->token = $token;
    }

    public function message()
    {
        $userInfos = $this->em->getRepository('KumaBroodjesBundle:UserInfo')->findAll();
        foreach ($userInfos as $userInfo) {
            if ($userInfo->getSlackId() !== '' && $userInfo->getCredits() < 5 && $userInfo->getCredits() != 0) {
                $this->webHook->sendCurl('@' . $userInfo->getSlackName() . '', 'Je credits zijn onder 5 euro!');
            }
        }
    }
}
