<?php

namespace Kuma\BroodjesBundle\Controller;

use Kuma\BroodjesBundle\Entity\DefaultOrder;
use Kuma\BroodjesBundle\Entity\EndProduct;
use Kuma\BroodjesBundle\Entity\LunchOrder;
use Kunstmaan\AdminBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
    /**
     * The index action will render the main screen the users see when they log in in to the admin
     *
     * @Route("/", name="kuma_broodjes_dashboard")
     */
    public function widgetAction()
    {
        /** @var User $user */
        $user = $this->getUser();

        $order = $this->getDoctrine()->getRepository('KumaBroodjesBundle:LunchOrder')->findBy(
            ['user' => $user, 'status' => 0]
        );
        $endProducts = $this->getDoctrine()->getRepository('KumaBroodjesBundle:EndProduct')->findBy(
            ['user' => $user]
        );
        $dayItems = $this->getDoctrine()->getRepository('KumaBroodjesBundle:DayItem')->findAll();
        $userInfo = $this->getDoctrine()->getRepository('KumaBroodjesBundle:UserInfo')->findBy(['user' => $user]);
        $users = $this->getDoctrine()->getRepository('KunstmaanAdminBundle:User')->findAll();

        $favorites = [];

        foreach ($endProducts as $endProduct) {
            if ($endProduct->isFavorite() == 1) {
                $favorites[] = $endProduct;
            }
        }

        $defaultOrders = $this->getDoctrine()->getRepository('KumaBroodjesBundle:DefaultOrder')->findBy(
            ['user' => $user]
        );
        $createDay = function ($dayNumber) {
            return mktime(null, null, null, null, $dayNumber);
        };
        $weekDays = [$createDay(0), $createDay(1), $createDay(2), $createDay(3), $createDay(4)];

        $today = date('l');
        foreach ($dayItems as $item) {
            if ($item->getDay() === $today) {
                $item->{'cssStyle'} = 'color:#2997ce;';
            } else {
                $item->{'cssStyle'} = '';
            }
        }

        $clientId = $this->getParameter('slack.api.client.id');

        return $this->render(
            'KumaBroodjesBundle:Dashboard:index.html.twig',
            [
                'users' => $users,
                'order' => $order,
                'endProducts' => $endProducts,
                'favorites' => $favorites,
                'dayItems' => $dayItems,
                'userInfo' => $userInfo[0],
                'clientId' => $clientId,
                'defaultOrders' => $defaultOrders,
                'weekDays' => $weekDays,
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     * @Route("/default/toggle/{userId}", name="kuma_broodjes_toggle_default")
     */
    public function toggleDefaultAction(Request $request, $userId)
    {
        $userInfo = $this->getDoctrine()->getRepository('KumaBroodjesBundle:UserInfo')->findBy(['user' => $userId]);
        $this->denyAccessUnlessGranted('toggle', $userInfo[0]);

        $url = $request->headers->get('referer');
        $session = $request->getSession();
        $translator = $this->get('translator');

        if ($userInfo[0]->getDefaultToggle() == 0) {
            $toggle = $translator->trans(
                'flashbag.notice.default.toggle.pause'
            );
            $userInfo[0]->setDefaultToggle(1);
        } else {
            $toggle = $translator->trans(
                'flashbag.notice.default.toggle.play'
            );
            $userInfo[0]->setDefaultToggle(0);
        }

        $session->getFlashBag()->add('info', $toggle);

        $this->getDoctrine()->getManager()->persist($userInfo[0]);
        $this->getDoctrine()->getManager()->flush();

        return new RedirectResponse(
            $url
        );
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     * @Route("/remove/order/{orderId}/{endProdId}", name="kuma_broodjes_remove_order")
     */
    public function removeCurrentOrderAction(Request $request, $orderId, $endProdId)
    {
        $user = $this->getUser();
        /** @var LunchOrder $order */
        $order = $this->getDoctrine()->getRepository('KumaBroodjesBundle:LunchOrder')->findBy(
            ['id' => $orderId, 'user' => $user, 'status' => 0]
        );
        $translator = $this->get('translator');
        $url = $request->headers->get('referer');
        $session = $request->getSession();
        $endProduct = $this->getDoctrine()->getRepository('KumaBroodjesBundle:EndProduct')->findBy(['id' => $endProdId, 'user' => $user]);

        if (!count($order) || !count($endProduct)) {
            $orderError = $translator->trans(
                'flashbag.notice.order.endproduct.remove.fail'
            );
            $session->getFlashBag()->add('warning', $orderError);

            return new RedirectResponse(
                $url
            );
        }
        $newCost = $order[0]->getPrice() - $endProduct[0]->getPrice();

        $order[0]->removeEndProduct($endProduct[0]);
        $order[0]->setPrice($newCost);

        $toggle = $translator->trans(
            'flashbag.notice.order.endproduct.remove.success',
            ['%name%' => $endProduct[0]->getName()]
        );
        $session->getFlashBag()->add('success', $toggle);

        if (count($order[0]->getEndProducts()) == 0) {
            $this->getDoctrine()->getManager()->remove($order[0]);
        } else {
            $this->getDoctrine()->getManager()->persist($order[0]);
        }
        $this->getDoctrine()->getManager()->flush();

        return new RedirectResponse(
            $url
        );
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     * @Route("/order/dayitem/{type}", name="kuma_broodjes_order_dayitem")
     */
    public function orderDayItemAction(Request $request, $type)
    {
        $user = $this->getUser();
        $translator = $this->get('translator');
        $url = $request->headers->get('referer');
        $session = $request->getSession();

        $endProducts = $this->getDoctrine()->getRepository('KumaBroodjesBundle:EndProduct')->findBy(['user' => $user]);
        $userInfo = $this->getDoctrine()->getRepository('KumaBroodjesBundle:UserInfo')->findBy(['user' => $user]);
        $orderProduct = '';
        if ($type == 'broodje') {
            $productName = 'Dagbroodje';
            $slackname = 'db';
            $prod = $translator->trans(
                'flashbag.notice.order.endproduct.sandwich'
            );
        } elseif ($type == 'soep') {
            $productName = 'Dagsoep';
            $slackname = 'ds';
            $prod = $translator->trans(
                'flashbag.notice.order.endproduct.soup'
            );
        } else {
            return new RedirectResponse(
                $url
            );
        }

        foreach ($endProducts as $endProduct) {
            if ($endProduct->getProduct()->getName() == $productName) {
                $orderProduct = $endProduct;
            }
        }

        if (!$orderProduct instanceof EndProduct) {
            $product = $this->getDoctrine()->getRepository('KumaBroodjesBundle:Product')->findBy(
                ['name' => $productName]
            );
            $orderProduct = new EndProduct();
            $orderProduct->setUser($user);
            $orderProduct->setProduct($product[0]);
            $orderProduct->setslackName($slackname);
            $this->getDoctrine()->getManager()->persist($orderProduct);
            $this->getDoctrine()->getManager()->flush();
        }
        $order = $this->getDoctrine()->getRepository('KumaBroodjesBundle:LunchOrder')->findBy(
            ['user' => $user, 'status' => 0]
        );

        if (count($order)) {
            $newPrice = $order[0]->getPrice() + $orderProduct->getPrice();
            if ($userInfo[0]->getCredits() < $newPrice) {
                $money = $translator->trans('flashbag.notice.order.money');
                $session->getFlashBag()->add('danger', $money);

                return new RedirectResponse(
                    $url
                );
            }
            if (count($order)) {
                foreach ($order[0]->getEndProducts() as $p) {
                    if ($p->getId() === $orderProduct->getId()) {
                        $duplication = $translator->trans('flashbag.notice.order.duplication');
                        $session->getFlashBag()->add('danger', $duplication);

                        return new RedirectResponse(
                            $url
                        );
                    }
                }
            }

            $order[0]->addEndProduct($orderProduct);
            $order[0]->setPrice($newPrice);
            $this->getDoctrine()->getManager()->persist($order[0]);
        } else {
            if ($userInfo[0]->getCredits() < $orderProduct->getPrice()) {
                $money = $translator->trans('flashbag.notice.order.money');
                $session->getFlashBag()->add('danger', $money);

                return new RedirectResponse(
                    $url
                );
            }
            $newOrder = new LunchOrder();
            $newOrder->setUser($user);
            $newOrder->setPrice($orderProduct->getPrice());
            $newOrder->addEndProduct($orderProduct);
            $this->getDoctrine()->getManager()->persist($newOrder);
        }

        $session->getFlashBag()->add('success', $prod);

        $this->getDoctrine()->getManager()->flush();

        return new RedirectResponse(
            $url
        );
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     * @Route("/order/defaultday/toggle/{defaultId}", name="kuma_broodjes_toggle_default_day")
     */
    public function defaultDayToggleAction(Request $request, $defaultId)
    {
        $translator = $this->get('translator');
        $url = $request->headers->get('referer');
        $session = $request->getSession();

        $defaultOrder = $this->getDoctrine()->getRepository('KumaBroodjesBundle:DefaultOrder')->find($defaultId);
        $createDay = function ($dayNumber) {
            return mktime(null, null, null, null, $dayNumber);
        };

        if ($defaultOrder->getPause() == 0) {
            $defaultday = $translator->trans(
                'flashbag.notice.defaultday.pause',
                ['%day%' => date('l', $createDay($defaultOrder->getDay() - 1))]
            );
            $defaultOrder->setPause(1);
        } else {
            $defaultday = $translator->trans(
                'flashbag.notice.defaultday.play',
                ['%day%' => date('l', $createDay($defaultOrder->getDay() - 1))]
            );
            $defaultOrder->setPause(0);
        }

        $session->getFlashBag()->add('info', $defaultday);

        $this->getDoctrine()->getEntityManager()->persist($defaultOrder);
        $this->getDoctrine()->getEntityManager()->flush();

        return new RedirectResponse(
            $url
        );
    }

    /**
     * @param Request $request
     *
     * @Route("/order/defaultday/set", name="kuma_broodjes_set_default_day")
     *
     * @return JsonResponse
     */
    public function DefaultDayOrderAction(Request $request)
    {
        $day = $request->request->get('day');
        $productId = $request->request->get('product');
        $user = $this->getUser();

        if ($day < 1 || $day > 5) {
            return new JsonResponse(
                null, 401, [
                    'Content-Type' => 'application/json',
                ]
            );
        }

        $defaultOrder = $this->getDoctrine()->getRepository('KumaBroodjesBundle:DefaultOrder')->findBy(
            ['user' => $user, 'day' => $day]
        );

        if ($productId <= 0) {
            $defaultOrder[0]->setEndProduct(null);
            $price = 0;
        } else {
            $endProduct = $this->getDoctrine()->getRepository('KumaBroodjesBundle:EndProduct')->find($productId);
            $this->denyAccessUnlessGranted('edit', $endProduct);
            $defaultOrder[0]->setEndProduct($endProduct);
            $price = $endProduct->getPrice();
        }

        $this->getDoctrine()->getEntityManager()->persist($defaultOrder[0]);
        $this->getDoctrine()->getEntityManager()->flush();

        return new JsonResponse(
            ['price' => $price], 200, [
                'Content-Type' => 'application/json',
            ]
        );
    }
}
