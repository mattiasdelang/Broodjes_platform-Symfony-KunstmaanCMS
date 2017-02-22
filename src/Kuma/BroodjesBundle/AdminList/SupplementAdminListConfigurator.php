<?php

namespace Kuma\BroodjesBundle\AdminList;

use Doctrine\ORM\EntityManager;

use Doctrine\ORM\QueryBuilder;
use Kuma\BroodjesBundle\Form\SupplementAdminType;
use Kunstmaan\AdminListBundle\AdminList\FieldAlias;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;

/**
 * The admin list configurator for ProductVariation
 */
class SupplementAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /**
     * @param EntityManager $em The entity manager
     * @param AclHelper $aclHelper The acl helper
     */
    public function __construct(EntityManager $em, AclHelper $aclHelper = null)
    {
        parent::__construct($em, $aclHelper);
        $this->setAdminType(new SupplementAdminType());
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('name', 'Name', true);
        $this->addField('categories', 'Categories', false);
        $this->addField('price', 'Price', true);
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        $this->addFilter('name', new ORM\StringFilterType('name'), 'Name');
        $this->addFilter('price', new ORM\NumberFilterType('price'), 'Price');
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
        return 'Supplement';
    }

    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->addSelect('c')
            ->join('b.categories', 'c');
    }
}
