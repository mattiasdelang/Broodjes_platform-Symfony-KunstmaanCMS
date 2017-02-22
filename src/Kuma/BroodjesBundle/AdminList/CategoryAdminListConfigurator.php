<?php

namespace Kuma\BroodjesBundle\AdminList;

use Doctrine\ORM\EntityManager;

use Kuma\BroodjesBundle\Form\CategoryAdminType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\SortableInterface;

/**
 * The admin list configurator for Category
 */
class CategoryAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /**
     * @param EntityManager $em The entity manager
     * @param AclHelper $aclHelper The acl helper
     */
    public function __construct(EntityManager $em, AclHelper $aclHelper = null)
    {
        parent::__construct($em, $aclHelper);
        $this->setAdminType(new CategoryAdminType());
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('name', 'Name', true);
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
    }

    /**
     * Get bundle name
     *
     * @return string
     */
    public function getBundleName()
    {
        return 'KumaBroodjesBundle';
    }

    /**
     * Get entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'Category';
    }
}
