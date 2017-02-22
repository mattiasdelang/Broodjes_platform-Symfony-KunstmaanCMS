<?php

namespace Kuma\BroodjesBundle\Form\EventListener;

use Doctrine\ORM\EntityManager;
use Kuma\BroodjesBundle\Entity\EndProduct;
use Kuma\BroodjesBundle\Entity\LunchOrder;
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
class AddOrderProductListener implements EventSubscriberInterface
{
    private $em;
    private $translator;
    private $token;
    private $router;

    public function __construct(EntityManager $em, Translator $translator, TokenStorage $token, Router $router)
    {
        $this->em = $em;
        $this->translator = $translator;
        $this->token = $token;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            AdminListEvents::POST_ADD => 'onAddOrderProduct',
            AdminListEvents::PRE_ADD => 'onCheckDuplicateEndProduct',
        ];
    }

    public function onAddOrderProduct(AdminListEvent $event)
    {
        $request = $event->getRequest();
        // check if save & order button is clicked
        if ($request->request->has('saveorder')) {
            $session = $event->getRequest()->getSession();
            $endproduct = $event->getEntity();
            $user = $this->token->getToken()->getUser();
            /** @var LunchOrder $order */
            $order = $this->em->getRepository('KumaBroodjesBundle:LunchOrder')->isOrderToday($user);
            $userInfo = $this->em->getRepository('KumaBroodjesBundle:UserInfo')->findBy(['user' => $user]);

            $endProdPrice = $endproduct->getPrice();
            $orderCheck = count($order);

            // check if there is more than 1 order for today for the current user
            if ($orderCheck > 1) {
                $multipleError = $this->translator->trans('flashbag.notice.order.error.multiple');
                $session->getFlashBag()->add('danger', $multipleError);

                return;
            }
            // there is no order
            if ($orderCheck == 0) {
                //check if the user can pay the new endproduct order
                if ($userInfo[0]->getCredits() < $endProdPrice) {
                    $money = $this->translator->trans('flashbag.notice.order.money');
                    $session->getFlashBag()->add('danger', $money);

                    return;
                }
                $newOrder = new LunchOrder();
                $newOrder->setUser($user);
                $newOrder->setDate(new \DateTime());
                $newOrder->addEndProduct($endproduct);
                $newOrder->setPrice($endProdPrice);
                $this->em->persist($newOrder);

                $addOrder = $this->translator->trans(
                    'flashbag.notice.order.add',
                    ['%name%' => $endproduct->getProduct()->getName()]
                );
                $session->getFlashBag()->add('info', $addOrder);
            } else { // there is already 1 order for today for the current user
                $currentPrice = $order[0]->getPrice();
                $newPrice = $currentPrice + $endProdPrice;

                // check if the user can pay the new endproduct, plus the current order
                if ($userInfo[0]->getCredits() < $newPrice) {
                    $money = $this->translator->trans('flashbag.notice.order.money');
                    $session->getFlashBag()->add('danger', $money);

                    return;
                }
                $order[0]->addEndProduct($endproduct);
                $order[0]->setPrice($newPrice);

                $this->em->persist($order[0]);

                $addProduct = $this->translator->trans(
                    'flashbag.notice.order.endproduct.add',
                    ['%name%' => $endproduct->getProduct()->getName()]
                );
                $session->getFlashBag()->add('info', $addProduct);
            }
            $this->em->flush();
        }
    }

    public function onCheckDuplicateEndProduct(AdminListEvent $event)
    {
        if ($event->getEntity() instanceof EndProduct) {
            $supplementIds = [];
            $result = '';

            foreach ($event->getEntity()->getSupplements() as $supplement) {
                $supplementIds[] = $supplement->getId();
            }
            sort($supplementIds);

            $endProducts = $this->em->getRepository('KumaBroodjesBundle:EndProduct')->findBy(
                [
                    'product' => $event->getEntity()->getProduct(),
                    'user' => $this->token->getToken()->getUser(),
                    'deletedAt' => null,
                ]
            );

            /** @var EndProduct $endProduct */
            foreach ($endProducts as $endProduct) {
                $suppsIds = [];
                foreach ($endProduct->getSupplements() as $supps) {
                    $suppsIds[] = $supps->getId();
                }
                sort($suppsIds);

                if ($suppsIds == $supplementIds) {
                    $result = true;
                }
            }
            if ($result) {
                $money = $this->translator->trans('flashbag.notice.endproduct.error.multiple');
                $event->getRequest()->getSession()->getFlashBag()->add('info', $money);

                $url = new RedirectResponse(
                    $this->router->generate('kumabroodjesbundle_admin_endproduct_add')
                );
                $event->setResponse($url);
            }
        }
    }
}
