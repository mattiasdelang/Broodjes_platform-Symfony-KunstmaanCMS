<?php

namespace Kuma\BroodjesBundle\AdminList;

use Doctrine\ORM\EntityManager;

use Doctrine\ORM\QueryBuilder;
use Kuma\BroodjesBundle\Form\ProductAdminType;
use Kunstmaan\AdminListBundle\AdminList\FieldAlias;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * The admin list configurator for Product
 */
class ProductAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /**
     * @param EntityManager $em The entity manager
     * @param AclHelper $aclHelper The acl helper
     */
    private $auth;

    public function __construct(EntityManager $em, AclHelper $aclHelper = null, AuthorizationCheckerInterface $auth)
    {
        parent::__construct($em, $aclHelper);
        $this->setAdminType(new ProductAdminType());
        $this->auth = $auth;
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('name', 'Name', true);
        $this->addField('price', 'Price', true);
        $this->addField('ingredients', 'Ingredients', true);
        $this->addField('c.name', 'Category', true, null, new FieldAlias('c', 'category'));
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        $this->addFilter('name', new ORM\StringFilterType('name'), 'Name');
        $this->addFilter('price', new ORM\NumberFilterType('price'), 'Price');
        $this->addFilter('category', new ORM\StringFilterType('name', 'c'), 'Category');
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
        return 'Product';
    }

    public function canAdd()
    {
        if ($this->auth->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        return false;
    }

    public function canEdit($item)
    {
        if ($this->auth->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        return false;
    }

    public function canDelete($item)
    {
        if ($this->auth->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        return false;
    }

    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->addSelect('c')
            ->join('b.category', 'c');
    }
}
