<?php

namespace Kuma\BroodjesBundle\Helper\Menu;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\AdminPanel\AdminPanelAction;
use Kunstmaan\AdminBundle\Helper\AdminPanel\AdminPanelActionInterface;
use Kunstmaan\AdminBundle\Helper\AdminPanel\AdminPanelAdaptorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class UserCreditAdaptor implements AdminPanelAdaptorInterface
{
    /**
     * @return AdminPanelActionInterface[]
     */
    private $em;
    private $token;

    public function __construct(EntityManager $em, TokenStorage $token)
    {
        $this->em = $em;
        $this->token = $token;

    }

    public function getAdminPanelActions()
    {
        return array(
            $this->getCreditAction(),
        );
    }

    private function getCreditAction()
    {
        $user = $this->token->getToken()->getUser();
        $userInfo = $this->em->getRepository('KumaBroodjesBundle:UserInfo')->findBy(['user' => $user]);

        return new AdminPanelAction(
            array(
                'path' => 'kumabroodjesbundle_admin_transaction',
            ),
            '€ '.$userInfo[0]->getCredits(),
            '',
            ''
        );
    }
}
