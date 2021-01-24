<?php

namespace Alita\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class Builder
{
    protected FactoryInterface $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function mainMenu(?array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Home', [
            'route'           => 'alita_dashboard',
            'labelAttributes' => [
                'icon' => 'fas fa-tachometer-alt',
            ],
        ]);
        /*
        // create another menu item
        $menu->addChild('About Me', ['route' => 'alita_dashboard']);
        // you can also add sub levels to your menus as follows
        $menu['About Me']->addChild('Edit profile', ['route' => 'alita_dashboard', 'labelAttributes' => [
            'icon' => 'fas fa-tachometer-alt',
        ]]);
        */

        return $menu;
    }
}
