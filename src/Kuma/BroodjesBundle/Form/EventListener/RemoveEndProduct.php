<?php

namespace Kuma\BroodjesBundle\Form\EventListener;

use Doctrine\ORM\EntityManager;
use Kuma\BroodjesBundle\Entity\EndProduct;
use Kunstmaan\AdminListBundle\Event\AdminListEvent;
use Kunstmaan\AdminListBundle\Event\AdminListEvents;
use Kunstmaan\TranslatorBundle\Service\Translator\Translator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class RemoveEndProduct implements EventSubscriberInterface
{
    private $em;
    private $token;
    private $translator;

    public function __construct(EntityManager $em, TokenStorage $token, Translator $translator)
    {
        $this->em = $em;
        $this->token = $token;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            AdminListEvents::PRE_DELETE => 'onRemoveEndProduct',
        ];
    }

    public function onRemoveEndProduct(AdminListEvent $event)
    {
        $url = $event->getRequest()->headers->get('referer');

        $entity = $event->getEntity();
        if ($entity instanceof EndProduct) {
            $lunchOrder = $this->em->getRepository('KumaBroodjesBundle:LunchOrder')->endProdInOpenOrder($entity);

            if (count($lunchOrder)) {
                $error = $this->translator->trans('flashbag.notice.endproduct.remove');
                $event->getRequest()->getSession()->getFlashBag()->add('danger', $error);

                $url = new RedirectResponse(
                    $url
                );
                $event->setResponse($url);
            }

            $defaultOrders = $this->em->getRepository('KumaBroodjesBundle:DefaultOrder')->findBy(['user' => $entity->getUser()]);

            foreach ($defaultOrders as $defaultOrder) {
                if ($defaultOrder->getEndProduct() == $entity) {
                    $defaultOrder->setEndProduct(null);
                    $this->em->persist($defaultOrder);
                }
            }
        }
    }
}
