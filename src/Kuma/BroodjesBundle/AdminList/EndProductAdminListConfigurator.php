<?php

namespace Kuma\BroodjesBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use FOS\UserBundle\Model\UserInterface;
use Kunstmaan\AdminBundle\Entity\EntityInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FieldAlias;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Kunstmaan\AdminListBundle\AdminList\ItemAction\SimpleItemAction;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * The admin list configurator for EndProduct
 */
class EndProductAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
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

    public function setCustomItemsActions()
    {
        $configurator = $this;

        $actions = [
            // ["_default", "heart", "Set as default"],
            // ["_favorite", "star", "Set as favorite"],
            ['_order', 'shopping-cart', 'Order product'],
        ];

        foreach ($actions as $action) {
            $route = function (EntityInterface $item) use ($configurator, $action) {
                return [
                    'path' => $configurator->getPathByConvention() . $action[0],
                    'params' => ['id' => $item->getId()],
                ];
            };

            $action = new SimpleItemAction($route, $action[1], $action[2]);
            $configurator->addItemAction($action);
        }
    }

    public function setSaveOrderButton()
    {
        $configurator = $this;

        $action = new SimpleItemAction(
            '',
            '',
            '',
            'KumaBroodjesBundle:AdminList/EndProduct:save_and_order_button.html.twig'
        );
        $configurator->addItemAction($action);
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        if ($this->auth->isGranted('ROLE_SUPER_ADMIN')) {
            $this->addField('u.username', 'User', true, null, new FieldAlias('u', 'user'));
        }
        $this->addField('slackName', 'Slack Name', true);
        $this->addField('p.name', 'Product', true, null, new FieldAlias('p', 'product'));
        $this->addField('supplements', 'Supplements', false);
        $this->addField('extraInfo', 'Extra Info', true);
        $this->addField(
            'isFavorite',
            'Favorite',
            true,
            'KumaBroodjesBundle:AdminList/EndProduct:favorite_field.html.twig'
        );
        $this->addField('price', 'Price', false);
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        if ($this->auth->isGranted('ROLE_SUPER_ADMIN')) {
            $this->addFilter('user', new ORM\StringFilterType('username', 'u'), 'User');
        }
        $this->addFilter('product', new ORM\StringFilterType('name', 'p'), 'Product');
        $this->addFilter('extraInfo', new ORM\StringFilterType('extraInfo'), 'Extra info');
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
        return 'EndProduct';
    }

    public function getEditTemplate()
    {
        return 'KumaBroodjesBundle:AdminList/EndProduct:add_or_edit.html.twig';
    }

    public function getAddTemplate()
    {
        return 'KumaBroodjesBundle:AdminList/EndProduct:add_or_edit.html.twig';
    }

    public function getListTemplate()
    {
        return 'KumaBroodjesBundle:AdminList/EndProduct:list.html.twig';
    }

    public function canEdit($item)
    {
        return false;
    }

    public function canDelete($item)
    {
        return true;
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->addSelect('p')
            ->join('b.product', 'p')
            ->addOrderBy('b.isFavorite', 'DESC');

        if ($this->auth->isGranted('ROLE_SUPER_ADMIN')) {
            $queryBuilder
                ->addSelect('u', 'p')
                ->join('b.user', 'u')
                ->orderBy('u.username', 'asc');

            return;
        }
        $queryBuilder
            ->where('b.user = ' . $this->user->getId());
    }
}
