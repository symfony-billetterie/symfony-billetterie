<?php

namespace AppBundle\Menu;

use AppBundle\Entity\User;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use AppBundle\Menu\Builder as BaseBuilder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminBuilder
 */
class AdminBuilder extends BaseBuilder
{
    /**
     * @param FactoryInterface $factory
     * @param array $options
     *
     * @return ItemInterface
     */
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $this->addItem($menu, 'admin.nav.home', 'admin_homepage', 'home');

        if ($this->getAuthorization()->isGranted(User::USER_ROLE_SUPER_ADMIN)) {
            $this->addItem($menu, 'admin.nav.log', 'admin_log_index', 'file-text');
        }

        /** @var Request $request */
        $request = $this->getRequest();
        $routeName = $request->get('_route');

        $eventType = $this->addItem($menu, 'admin.nav.event_type.title', 'admin_event_type_index', 'list');
        if (strpos($routeName, 'admin_event_type') === 0) {
            $eventType->setCurrent(true);
        }

        return $menu;
    }

    /**
     * @param FactoryInterface $factory
     *
     * @return ItemInterface
     */
    public function breadcrumb(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root');
        if ($this->getAuthorization()->isGranted(User::USER_ROLE_SUPER_ADMIN)) {
            $this->addItemIfRouteMatch('admin_log_index', $menu, 'admin_log_index', 'admin.nav.log', 'file-text');
        }

        /** @var Request $request */
        $request = $this->getRequest();
        $routeName = $request->get('_route');

        $eventType = $this->addItemIfRouteMatch('admin.nav.event_type.title', $menu, 'admin_event_type_index', 'list');
        if (strpos($routeName, 'admin_event_type') === 0) {
            $this->addItem($menu, 'admin.nav.event_type.index', 'admin_event_type_index', 'list');
            if (strpos($routeName, 'admin_event_type_add') === 0) {
                $this->addItem($menu, 'admin.nav.event_type.create', 'admin_event_type_add', 'plus');
            }
            if (strpos($routeName, 'admin_event_type_edit') === 0) {
                $eventType = $request->get('id');
                $this->addItem($menu, 'admin.nav.event_type.edit', 'admin_event_type_edit', 'pencil', ['id' => $eventType]);
            }
        }

        return $menu;
    }

    /**
     * @param $prefix
     * @param ItemInterface $menuItem
     * @param $route
     * @param $label
     * @param null $icon
     * @param array $routeParameters
     *
     * @return bool|ItemInterface
     */
    public function addItemIfRouteMatch(
        $prefix,
        ItemInterface $menuItem,
        $route,
        $label,
        $icon = null,
        $routeParameters = []
    )
    {
        $routeName = $this->getRequest()->get('_route');
        if (strpos($routeName, $prefix) === 0) {
            $menuItem = $this->addItem($menuItem, $label, $route, $icon, $routeParameters);
        }

        return $menuItem;
    }
}
