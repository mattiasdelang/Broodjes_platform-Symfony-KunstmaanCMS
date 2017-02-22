<?php

namespace Kuma\BroodjesBundle\Form\EventListener;

use Doctrine\ORM\EntityManager;
use Kuma\BroodjesBundle\Entity\EndProduct;
use Kuma\BroodjesBundle\Entity\LunchOrder;
use Kuma\BroodjesBundle\Entity\UserInfo;
use Kunstmaan\AdminListBundle\Event\AdminListEvent;
use Kunstmaan\AdminListBundle\Event\AdminListEvents;
use Kunstmaan\TranslatorBundle\Service\Translator\Translator;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class EditLunchOrderListener implements EventSubscriberInterface
{
    private $em;
    private $translator;
    private $token;
    private $router;
    private $mailer;

    public function __construct(EntityManager $em, Translator $translator, TokenStorage $token, Router $router, \Swift_Mailer $mailer)
    {
        $this->em = $em;
        $this->translator = $translator;
        $this->token = $token;
        $this->router = $router;
        $this->mailer = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            AdminListEvents::PRE_EDIT => 'onEditLunchOrder',
        ];
    }

    public function onEditLunchOrder(AdminListEvent $event)
    {
        /** @var LunchOrder $order */
        $entity = $event->getEntity();

        if ($entity instanceof LunchOrder) {
            $price = 0;
            /** @var EndProduct $endProduct */
            foreach ($entity->getEndProducts() as $endProduct) {
                $price += $endProduct->getPrice();
            }
            $userInfo = $this->em->getRepository('KumaBroodjesBundle:UserInfo')->findBy(['user' => $entity->getUser()]);
            $session = $event->getRequest()->getSession();

            if ($userInfo[0]->getCredits() >= $price) {
                if ($price === 0) {
                    $this->em->remove($entity);
                    $this->em->flush();

                    $url = new RedirectResponse(
                        $this->router->generate('kumabroodjesbundle_admin_lunchorder')
                    );
                    $event->setResponse($url);
                }

                $entity->setPrice($price);
            } else {
                $money = $this->translator->trans('flashbag.notice.order.money');
                $session->getFlashBag()->add('danger', $money);

                $url = new RedirectResponse(
                    $this->router->generate('kumabroodjesbundle_admin_lunchorder_edit', ['id' => $entity->getId()])
                );

                $event->setResponse($url);
            }
        }
    }
}
