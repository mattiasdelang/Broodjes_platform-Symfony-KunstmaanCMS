<?php

namespace Kuma\BroodjesBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FieldAlias;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * The admin list configurator for LunchOrder
 */
class LunchOrderAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /**
     * @param EntityManager $em The entity manager
     * @param AclHelper $aclHelper The acl helper
     */
    private $user;
    private $auth;

    public function __construct(
        EntityManager $em,
        AclHelper $aclHelper = null,
        AuthorizationCheckerInterface $auth,
        UserInterface $user
    ) {
        parent::__construct($em, $aclHelper);
        $this->user = $user;
        $this->auth = $auth;
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        if ($this->auth->isGranted('ROLE_SUPER_ADMIN')) {
            $this->addField('u.username', 'User', true, null, new FieldAlias('u', 'user'));
        }
        $this->addField('endProducts', 'Product', false, 'KumaBroodjesBundle:AdminList/LunchOrder:end_products_field.html.twig');
        $this->addField('price', 'Price', true);
        $this->addField('date', 'Date', true);
        //$this->addField('status', 'Changeable', true, 'KumaBroodjesBundle:AdminList/LunchOrder:status_field.html.twig');
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        if ($this->auth->isGranted('ROLE_SUPER_ADMIN')) {
            $this->addFilter('user', new ORM\StringFilterType('username', 'u'), 'User');
        }
        $this->addFilter('date', new ORM\DateFilterType('date'), 'Date');
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
        return 'LunchOrder';
    }

    public function canAdd()
    {
        return false;
    }

    public function canEdit($item)
    {
        if ($item->getStatus() == 1) {
            return false;
        }

        return true;
    }

    public function canDelete($item)
    {
        return false;
    }

    public function hasItemActions()
    {
        return false;
    }

    public function canExport()
    {
        if (true === $this->auth->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        return false;
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        if ($this->auth->isGranted('ROLE_SUPER_ADMIN')) {
            $queryBuilder
                ->addSelect('u')
                ->join('b.user', 'u')
                ->addOrderBy('u.username', 'asc');

            return;
        }
        $queryBuilder
            ->where('b.user = ' . $this->user->getId());
    }
}
