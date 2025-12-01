<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\CategoryFactory;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use AwoDev\Component\AwoCoupon\Administrator\Extension\AwocouponComponent;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class implements ServiceProviderInterface {
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function register( Container $container ) {
		$container->registerServiceProvider( new CategoryFactory( '\\AwoDev\\Component\\AwoCoupon' ) );
		//$container->registerServiceProvider( new MVCFactory( '\\AwoDev\\Component\\AwoCoupon' ) );
		$container->registerServiceProvider( new \AwoDev\Component\AwoCoupon\Administrator\Helper\Src\MVCFactoryService( '\\AwoDev\\Component\\AwoCoupon' ) );
		$container->registerServiceProvider( new ComponentDispatcherFactory( '\\AwoDev\\Component\\AwoCoupon' ) );
		$container->registerServiceProvider( new RouterFactory( '\\AwoDev\\Component\\AwoCoupon' ) );

		$container->set(
			ComponentInterface::class,
			function ( Container $container ) {
				$component = new AwocouponComponent( $container->get( ComponentDispatcherFactoryInterface::class ) );

				$component->setRegistry( $container->get( Registry::class ) );
				$component->setMVCFactory( $container->get( MVCFactoryInterface::class ) );
				$component->setRouterFactory( $container->get( RouterFactoryInterface::class ) );

				return $component;
			}
		);
	}
};
