<?php

namespace Kuma\BroodjesBundle\Form\EventListener;

use Doctrine\ORM\EntityManager;
use Kuma\BroodjesBundle\Entity\DefaultOrder;
use Kuma\BroodjesBundle\Entity\UserInfo;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\TranslatorBundle\Service\Translator\Translator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class CreateUserListener implements EventSubscriberInterface
{
    private $em;
    private $translator;

    public function __construct(EntityManager $em, Translator $translator)
    {
        $this->translator = $translator;
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onUserCreation',
        ];
    }

    public function onUserCreation(InteractiveLoginEvent $event)
    {

        /**
         * @var User
         */
        $user = $event->getAuthenticationToken()->getUser();
        $userInfoCheck = $this->em->getRepository('KumaBroodjesBundle:UserInfo')->findBy(['user' => $user]);
        $defaultOrderCheck = $this->em->getRepository('KumaBroodjesBundle:DefaultOrder')->findBy(['user' => $user]);
        $session = new Session();

        if (strpos($user->getUsername(), '@kunstmaan.be') !== false) {
            $userName = $this->translator->trans(
                'flashbag.username.change'
            );
            $session->getFlashBag()->add('warning', $userName);
        }

        $userInfoCount = count($userInfoCheck);
        $defaultOrderCount = count($defaultOrderCheck);

        if ($userInfoCount === 0) {
            $userInfo = new UserInfo();
            $userInfo->setCredits(0);
            $userInfo->setUser($user);

            $this->em->persist($userInfo);
            $this->em->flush();
        }
        if ($defaultOrderCount === 0) {
            for ($i = 1; $i < 6; ++$i) {
                $defOrder = new DefaultOrder();
                $defOrder->setUser($user);
                $defOrder->setPause(0);
                $defOrder->setDay($i);

                $this->em->persist($defOrder);
                $this->em->flush();
            }
        }
    }
}
