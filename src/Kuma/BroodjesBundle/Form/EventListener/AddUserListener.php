<?php

namespace Kuma\BroodjesBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class AddUserListener implements EventSubscriberInterface
{
    private $token;

    public function __construct(TokenStorage $token)
    {
        $this->token = $token;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT => 'onAddUser',
        ];
    }

    public function onAddUser(FormEvent $event)
    {
        $entity = $event->getData();
        $user = $this->token->getToken()->getUser();
        $entity->setUser($user);
    }
}
