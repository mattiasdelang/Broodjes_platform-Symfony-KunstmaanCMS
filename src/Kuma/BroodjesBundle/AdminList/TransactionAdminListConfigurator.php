<?php

namespace Kuma\BroodjesBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Kuma\BroodjesBundle\Form\TransactionAdminType;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FieldAlias;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * The admin list configurator for Transaction
 */
class TransactionAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /**
     * @param EntityManager $em The entity manager
     * @param AclHelper $aclHelper The acl helper
     */
    private $auth;
    private $user;

    public function __construct(
        EntityManager $em,
        AclHelper $aclHelper = null,
        AuthorizationCheckerInterface $auth,
        UserInterface $user
    ) {
        parent::__construct($em, $aclHelper);
        $this->setAdminType(new TransactionAdminType());
        $this->auth = $auth;
        $this->user = $user;
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        if ($this->auth->isGranted('ROLE_SUPER_ADMIN')) {
            $this->addField('u.username', 'User', true, null, new FieldAlias('u', 'user'));
            $this->addField('mollieTransactionId', 'Mollie Id', true);
        }
        $this->addField('method', 'Pay method', true);
        $this->addField('status', 'status', true);
        $this->addField('credits', 'Credits', true);
        $this->addField('createDate', 'Date', true);
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        if ($this->auth->isGranted('ROLE_SUPER_ADMIN')) {
            $this->addFilter('user', new ORM\StringFilterType('username', 'u'), 'User');
        }
        $this->addFilter('method', new ORM\StringFilterType('method'), 'Method');
        $this->addFilter('credits', new ORM\NumberFilterType('credits'), 'Credits');
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
        return 'Transaction';
    }

    public function canDelete($template)
    {
        return false;
    }

    public function canEdit($template)
    {
        return false;
    }

    public function canAdd()
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
                ->join('b.user', 'u');

            return;
        }

        $queryBuilder
            ->where('b.user = ' . $this->user->getId());
    }
}
