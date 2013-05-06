<?php

namespace Etu\Core\CoreBundle\Menu\AdminMenu;

use Etu\Core\CoreBundle\Menu\Sidebar\SidebarBuilder;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Admin menu.
 */
class AdminBuilder extends SidebarBuilder
{
	/**
	 * @param Router $router
	 */
	public function __construct(Router $router)
	{
		$this->blocks = array();
		$this->lastPosition = 0;

		$this
			->addBlock('base.admin_menu.title')
				->add('base.admin_menu.items.dashboard')
					->setIcon('edit-list.png')
					->setUrl($router->generate('admin_index'))
				->end()
				->add('base.admin_menu.items.modules')
					->setIcon('gear.png')
					->setUrl($router->generate('admin_modules'))
				->end()
				->add('base.admin_menu.items.pages')
					->setIcon('book.png')
					->setUrl($router->generate('admin_pages'))
				->end()
				->add('base.admin_menu.items.users')
					->setIcon('users.png')
					->setUrl($router->generate('admin_users_index'))
				->end()
				->add('base.admin_menu.items.stats')
					->setIcon('chart.png')
					->setUrl($router->generate('admin_stats'))
				->end()
			->end()
		;
	}
}
