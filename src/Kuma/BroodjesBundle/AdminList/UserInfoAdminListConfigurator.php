<?php

namespace Kuma\BroodjesBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Kuma\BroodjesBundle\Form\UserInfoAdminType;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FieldAlias;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;

/**
 * The admin list configurator for UserInfo
 */
class UserInfoAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /**
     * @param EntityManager $em        The entity manager
     * @param AclHelper     $aclHelper The acl helper
     */
    public function __construct(EntityManager $em, AclHelper $aclHelper = null)
    {
        parent::__construct($em, $aclHelper);
        $this->setAdminType(new UserInfoAdminType());
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('u.username', 'User', true, null, new FieldAlias('u', 'user'));
        $this->addField('credits', 'Credits', true);
        $this->addField('defaultToggle', 'Toggle Default', true, 'KumaBroodjesBundle:AdminList/UserInfo:default_toggle.html.twig');
        $this->addField('updated', 'Updated', true);
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        $this->addFilter('credits', new ORM\NumberFilterType('credits'), 'Credits');
        $this->addFilter('updated', new ORM\DateFilterType('updated'), 'Updated');
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
        return 'UserInfo';
    }

    public function canDelete($item)
    {
        return false;
    }

    public function canEdit($item)
    {
        return false;
    }

    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->addSelect('u')
            ->join('b.user', 'u')
            ->addOrderBy('u.username', 'asc');
    }
}
