<?php

namespace Kuma\BroodjesBundle\Controller;

use Kuma\BroodjesBundle\AdminList\TransactionAdminListConfigurator;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * The admin list controller for Transaction
 */
class TransactionAdminListController extends AdminListController
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
            $this->configurator = new TransactionAdminListConfigurator(
                $this->getEntityManager(),
                null,
                $this->get('security.authorization_checker'),
                $this->getUser()
            );
        }

        return $this->configurator;
    }

    /**
     * The index action
     *
     * @Route("/", name="kumabroodjesbundle_admin_transaction")
     */
    public function indexAction(Request $request)
    {
        return parent::doIndexAction($this->getAdminListConfigurator(), $request);
    }

    /**
     * The add action
     *
     * @Route("/add", name="kumabroodjesbundle_admin_transaction_add")
     * @Method({"GET", "POST"})
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
     * @Route("/{id}", requirements={"id" = "\d+"}, name="kumabroodjesbundle_admin_transaction_edit")
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
     * @Route("/{id}", requirements={"id" = "\d+"}, name="kumabroodjesbundle_admin_transaction_view")
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
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="kumabroodjesbundle_admin_transaction_delete")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function deleteAction(Request $request, $id)
    {
        return parent::doDeleteAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * The export action
     *
     * @param string $_format
     *
     * @Route("/export.{_format}", requirements={"_format" = "csv|xlsx"}, name="kumabroodjesbundle_admin_transaction_export")
     * @Method({"GET", "POST"})
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
     * @Route("/{id}/move-up", requirements={"id" = "\d+"}, name="kumabroodjesbundle_admin_transaction_move_up")
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
     * @Route("/{id}/move-down", requirements={"id" = "\d+"}, name="kumabroodjesbundle_admin_transaction_move_down")
     * @Method({"GET"})
     *
     * @return array
     */
    public function moveDownAction(Request $request, $id)
    {
        return parent::doMoveDownAction($this->getAdminListConfigurator(), $id, $request);
    }

}
