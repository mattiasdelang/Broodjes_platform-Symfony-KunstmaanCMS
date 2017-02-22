<?php

namespace Kuma\BroodjesBundle\Controller;

use Kuma\BroodjesBundle\AdminList\EndProductAdminListConfigurator;
use Kuma\BroodjesBundle\Entity\EndProduct;
use Kuma\BroodjesBundle\Entity\LunchOrder;
use Kuma\BroodjesBundle\Entity\UserInfo;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class EndProductAdminListController extends AdminListController
{
    /**
     * @var AdminListConfiguratorInterface
     */
    private $configurator;

    /**
     * @return AdminListConfiguratorInterface
     */
    public function getAdminListConfigurator()
    {
        if (!isset($this->configurator)) {
            $this->configurator = new EndProductAdminListConfigurator(
                $this->getEntityManager(),
                null,
                $this->get('security.authorization_checker'),
                $this->getUser()
            );
            $this->configurator->setAdminType($this->get('kumabroodjesbundle.endproduct.formtype'));
        }

        return $this->configurator;
    }

    /**
     * The index action
     *
     * @Route("/", name="kumabroodjesbundle_admin_endproduct")
     */
    public function indexAction(Request $request)
    {
        /** @var EndProductAdminListConfigurator $configurator */
        $configurator = $this->getAdminListConfigurator();
        $configurator->setCustomItemsActions();

        return parent::doIndexAction($configurator, $request);
    }

    /**
     * The add action
     *
     * @Route("/add", name="kumabroodjesbundle_admin_endproduct_add")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function addAction(Request $request)
    {
        return parent::doAddAction($this->getAdminListConfigurator(), null, $request);
    }

    /**
     * The edit action
     *
     * @param int $id
     *
     * @Route("/{id}", requirements={"id" = "\d+"}, name="kumabroodjesbundle_admin_endproduct_edit")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function editAction(Request $request, $id)
    {
        return parent::doEditAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * The edit action
     *
     * @param int $id
     *
     * @Route("/{id}", requirements={"id" = "\d+"}, name="kumabroodjesbundle_admin_endproduct_view")
     * @Method({"GET"})
     *
     * @return array
     */
    public function viewAction(Request $request, $id)
    {
        return parent::doViewAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * The delete action
     *
     * @param int $id
     *
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="kumabroodjesbundle_admin_endproduct_delete")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function deleteAction(Request $request, $id)
    {
        $endproduct = $this->getEntityManager()->getRepository('KumaBroodjesBundle:EndProduct')->find($id);
        $this->denyAccessUnlessGranted('delete', $endproduct);

        return parent::doDeleteAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * The export action
     *
     * @param string $_format
     *
     * @Route("/export.{_format}", requirements={"_format" = "csv|xlsx"}, name="kumabroodjesbundle_admin_endproduct_export")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function exportAction(Request $request, $_format)
    {
        return parent::doExportAction($this->getAdminListConfigurator(), $_format, $request);
    }

    /**
     * The move up action
     *
     * @param int $id
     *
     * @Route("/{id}/move-up", requirements={"id" = "\d+"}, name="kumabroodjesbundle_admin_endproduct_move_up")
     * @Method({"GET"})
     *
     * @return array
     */
    public function moveUpAction(Request $request, $id)
    {
        return parent::doMoveUpAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * The move down action
     *
     * @param int $id
     *
     * @Route("/{id}/move-down", requirements={"id" = "\d+"}, name="kumabroodjesbundle_admin_endproduct_move_down")
     * @Method({"GET"})
     *
     * @return array
     */
    public function moveDownAction(Request $request, $id)
    {
        return parent::doMoveDownAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * The move down action
     *
     * @param int $id
     *
     * @Route("/{id}/favorite", requirements={"id" = "\d+"}, name="kumabroodjesbundle_admin_endproduct_favorite")
     * @Method({"GET"})
     *
     * @return RedirectResponse
     */
    public function setAsFavorite(Request $request, $id)
    {
        $product = $this->getEntityManager()->getRepository('KumaBroodjesBundle:EndProduct')->find($id);
        $this->denyAccessUnlessGranted('fav', $product);

        $url = $request->headers->get('referer');
        $session = $request->getSession();
        $translator = $this->get('translator');

        if ($product->isFavorite() == 0) {
            $favorite = $translator->trans(
                'flashbag.notice.favorite.add',
                ['%name%' => $product->getProduct()->getName()]
            );
            $product->setIsFavorite(1);
        } else {
            $favorite = $translator->trans(
                'flashbag.notice.favorite.remove',
                ['%name%' => $product->getProduct()->getName()]
            );
            $product->setIsFavorite(0);
        }

        $session->getFlashBag()->add('info', $favorite);

        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();

        return new RedirectResponse(
            $url
        );
    }

    /**
     * The move down action
     *
     * @param int $id
     *
     * @Route("/{id}/order", requirements={"id" = "\d+"}, name="kumabroodjesbundle_admin_endproduct_order")
     * @Method({"GET"})
     *
     * @return RedirectResponse
     */
    public function orderProduct(Request $request, $id)
    {
        $session = $request->getSession();
        $translator = $this->get('translator');
        $user = $this->getUser();
        $url = $request->headers->get('referer');

        /** @var EndProduct $endproduct */
        $endproduct = $this->getEntityManager()->getRepository('KumaBroodjesBundle:EndProduct')->find($id);

        //if the user is owner of the product
        if ($user !== $endproduct->getUser()) {
            $userError = $translator->trans('flashbag.notice.order.error.user');
            $session->getFlashBag()->add('danger', $userError);

            return new RedirectResponse(
                $url
            );
        }

        if (!count($endproduct)) {
            $exist = $translator->trans('flashbag.notice.order.endproduct.exist');
            $session->getFlashBag()->add('info', $exist);

            return new RedirectResponse(
                $url
            );
        }
        /** @var LunchOrder $order */
        $order = $this->getEntityManager()->getRepository('KumaBroodjesBundle:LunchOrder')->isOrderToday($user);

        /** @var UserInfo $userInfo */
        $userInfo = $this->getEntityManager()->getRepository('KumaBroodjesBundle:UserInfo')->findBy(
            ['user' => $user]
        );

        $endProdPrice = $endproduct->getPrice();

        $orderCheck = count($order);

        //check if there is an order
        if ($orderCheck > 1) {
            $multipleError = $translator->trans('flashbag.notice.order.error.multiple');
            $session->getFlashBag()->add('danger', $multipleError);

            return new RedirectResponse(
                $url
            );
        }
        //no current order for today, make new one, and add endproduct
        if ($orderCheck == 0) {
            //check if user has enough credit to pay the endproduct
            if ($userInfo[0]->getCredits() < $endProdPrice) {
                $money = $translator->trans('flashbag.notice.order.money');
                $session->getFlashBag()->add('danger', $money);

                return new RedirectResponse(
                    $url
                );
            }
            $newOrder = new LunchOrder();
            $newOrder->setUser($user);
            $newOrder->addEndProduct($endproduct);
            $newOrder->setPrice($endProdPrice);
            $this->getEntityManager()->persist($newOrder);

            $addOrder = $translator->trans(
                'flashbag.notice.order.add',
                ['%name%' => $endproduct->getProduct()->getName()]
            );
            $session->getFlashBag()->add('info', $addOrder);
        } else { // there is already an order today, for this user
            $currentPrice = $order[0]->getPrice();
            $newPrice = $currentPrice + $endProdPrice;

            //check if product is already orded today, avoid duplication error
            foreach ($order[0]->getEndProducts() as $p) {
                if ($p->getId() === $endproduct->getId()) {
                    $duplication = $translator->trans('flashbag.notice.order.duplication');
                    $session->getFlashBag()->add('danger', $duplication);

                    return new RedirectResponse(
                        $url
                    );
                }
            }
            // check if the total(current order price + endproduct) credit can be payed
            if ($userInfo[0]->getCredits() < $newPrice) {
                $money = $translator->trans('flashbag.notice.order.money');
                $session->getFlashBag()->add('danger', $money);

                return new RedirectResponse(
                    $url
                );
            }
            $order[0]->addEndProduct($endproduct);
            $order[0]->setPrice($newPrice);
            $this->getEntityManager()->persist($order[0]);

            $addProduct = $translator->trans(
                'flashbag.notice.order.endproduct.add',
                ['%name%' => $endproduct->getProduct()->getName()]
            );
            $session->getFlashBag()->add('info', $addProduct);
        }
        $this->getEntityManager()->flush();

        return new RedirectResponse(
            $url
        );
    }
}
