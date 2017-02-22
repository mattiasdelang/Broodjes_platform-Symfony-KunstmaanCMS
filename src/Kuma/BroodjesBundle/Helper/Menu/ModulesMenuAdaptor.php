<?php

namespace Kuma\BroodjesBundle\Helper\Menu;

use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ModulesMenuAdaptor implements MenuAdaptorInterface
{
    /**
     * {@inheritdoc}
     */
    private $checker;

    public function __construct(AuthorizationCheckerInterface $checker)
    {
        $this->checker = $checker;
    }

    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)
    {
        if (!is_null($parent) && 'KunstmaanAdminBundle_modules' == $parent->getRoute()) {
            $menuItem = new TopMenuItem($menu);
            $menuItem->setRoute('kumabroodjesbundle_admin_product');
            $menuItem->setLabel('Product list');
            $menuItem->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
                $parent->setActive(true);
            }
            $children[] = $menuItem;
        }

        if (true === $this->checker->isGranted('ROLE_SUPER_ADMIN')) {
            if (!is_null($parent) && 'KunstmaanAdminBundle_modules' == $parent->getRoute()) {
                $menuItem = new TopMenuItem($menu);
                $menuItem->setRoute('kumabroodjesbundle_admin_supplement');
                $menuItem->setLabel('Supplement');
                $menuItem->setParent($parent);
                if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                    $menuItem->setActive(true);
                    $parent->setActive(true);
                }
                $children[] = $menuItem;
            }

            if (!is_null($parent) && 'KunstmaanAdminBundle_modules' == $parent->getRoute()) {
                $menuItem = new TopMenuItem($menu);
                $menuItem->setRoute('kumabroodjesbundle_admin_category');
                $menuItem->setLabel('Category');
                $menuItem->setParent($parent);
                if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                    $menuItem->setActive(true);
                    $parent->setActive(true);
                }
                $children[] = $menuItem;
            }

            if (!is_null($parent) && 'KunstmaanAdminBundle_modules' == $parent->getRoute()) {
                $menuItem = new TopMenuItem($menu);
                $menuItem->setRoute('kumabroodjesbundle_admin_dayitem');
                $menuItem->setLabel('DayItem');
                $menuItem->setParent($parent);
                if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                    $menuItem->setActive(true);
                    $parent->setActive(true);
                }
                $children[] = $menuItem;
            }

            if (!is_null($parent) && 'KunstmaanAdminBundle_modules' == $parent->getRoute()) {
                $menuItem = new TopMenuItem($menu);
                $menuItem->setRoute('kumabroodjesbundle_admin_userinfo');
                $menuItem->setLabel('User info');
                $menuItem->setParent($parent);
                if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                    $menuItem->setActive(true);
                    $parent->setActive(true);
                }
                $children[] = $menuItem;
            }
        }

        if (!is_null($parent) && 'KunstmaanAdminBundle_modules' == $parent->getRoute()) {
            $menuItem = new TopMenuItem($menu);
            $menuItem->setRoute('kumabroodjesbundle_admin_endproduct');
            $menuItem->setLabel('Your Products');
            $menuItem->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
                $parent->setActive(true);
            }
            $children[] = $menuItem;
        }

        if (!is_null($parent) && 'KunstmaanAdminBundle_modules' == $parent->getRoute()) {
            $menuItem = new TopMenuItem($menu);
            $menuItem->setRoute('kumabroodjesbundle_admin_lunchorder');
            $menuItem->setLabel('Order History');
            $menuItem->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
                $parent->setActive(true);
            }
            $children[] = $menuItem;
        }

        if (!is_null($parent) && 'KunstmaanAdminBundle_modules' == $parent->getRoute()) {
            $menuItem = new TopMenuItem($menu);
            $menuItem->setRoute('kumabroodjesbundle_admin_transaction');
            $menuItem->setLabel('Transaction');
            $menuItem->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
                $parent->setActive(true);
            }
            $children[] = $menuItem;
        }
    }
}
