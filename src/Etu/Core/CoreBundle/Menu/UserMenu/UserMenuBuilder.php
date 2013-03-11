<?php

namespace Etu\Core\CoreBundle\Menu\UserMenu;

/**
 * Default user menu. Edited by controllers on the fly.
 */
class UserMenuBuilder
{
	/**
	 * @var array
	 */
	protected $items;

	/**
	 * @var integer
	 */
	protected $lastPosition;

	/**
	 * @var integer
	 */
	protected $separatorCount;

	/**
	 * Constructor
	 * Initialize some default items.
	 *
	 * @param \Symfony\Bundle\FrameworkBundle\Routing\Router $router
	 */
	public function __construct(\Symfony\Bundle\FrameworkBundle\Routing\Router $router)
	{
		$this->items = array();
		$this->lastPosition = 0;
		$this->separatorCount = 0;

		$this
			->add('base.user.menu.flux')
				->setIcon('etu-icon-list')
				->setUrl('')
			->end()
			->add('base.user.menu.account')
				->setIcon('etu-icon-user')
				->setUrl('')
			->end()
			->add('base.user.menu.buckutt')
				->setIcon('etu-icon-duck')
				->setUrl('')
			->end()
			->add('base.user.menu.dailymail')
				->setIcon('etu-icon-megaphone')
				->setUrl('')
			->end()
			->add('base.user.menu.emails')
				->setIcon('etu-icon-mails')
				->setUrl('')
			->end()
			->add('base.user.menu.table')
				->setIcon('etu-icon-table')
				->setUrl('')
			->end()
			->add('base.user.menu.logout')
				->setIcon('etu-icon-power')
				->setUrl('')
			->end()
			->addSeparator()
			->add('base.user.menu.help')
				->setIcon('etu-icon-question')
				->setUrl('')
			->end()
		;
	}

	/**
	 * @param string $id
	 * @return \Etu\Core\CoreBundle\Menu\UserMenu\UserMenuItem
	 */
	public function add($id)
	{
		$this->lastPosition++;

		$this->items[$id] = new UserMenuItem($this, $id);
		$this->items[$id]->setPosition($this->lastPosition);

		return $this->items[$id];
	}

	/**
	 * @return \Etu\Core\CoreBundle\Menu\UserMenu\UserMenuBuilder
	 */
	public function addSeparator()
	{
		$this->lastPosition++;
		$this->separatorCount++;

		$item = new UserMenuSeparator($this);
		$item->setPosition($this->lastPosition);

		$this->items['separator-'.$this->separatorCount] = $item;

		return $this;
	}

	/**
	 * @param string $id
	 * @return UserMenuBuilder
	 */
	public function remove($id)
	{
		if (isset($this->items[$id])) {
			unset($this->items[$id]);
		}

		return $this;
	}

	/**
	 * @return array
	 */
	public function getItems()
	{
		return $this->items;
	}
}