<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Router\Route as JRoute;
use AwoDev\Component\AwoCoupon\Administrator\Helper\DiscountHelper;

class plgVmCouponAwoCoupon extends CMSPlugin {

	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);

		if ( method_exists( $this, 'registerLegacyListener' ) ) {
			$this->registerLegacyListener( 'plgVmValidateCouponCode' );
			$this->registerLegacyListener( 'plgVmCouponInUse' );
			$this->registerLegacyListener( 'plgVmRemoveCoupon' );
			$this->registerLegacyListener( 'plgVmCouponHandler' );
			$this->registerLegacyListener( 'plgVmUpdateTotals' );
		}
	}

	public function onAfterDispatch() {
		if ( ! class_exists( DiscountHelper::class ) ) {
			return;
		}

		if ( JFactory::getApplication()->isClient( 'administrator' ) ) {
			return;
		}			

		$option = JFactory::getApplication()->input->get( 'option' ); 
		if ( $option != 'com_virtuemart' ) {
			return;
		}

		$this->check_coupon_new();
		$this->check_coupon_delete();
		
		return;
	}

	public function plgVmValidateCouponCode( $_code, $_billTotal ) {
		if ( ! class_exists( DiscountHelper::class ) ) {
			return null;
		}
		return DiscountHelper::instance()->validating( $_code );
	}

	public function plgVmRemoveCoupon( $_code, $_force ) {
		if ( ! class_exists( DiscountHelper::class ) ) {
			return null;
		}
		return DiscountHelper::instance()->remove_coupon_code( $_code );
	}

	public function plgVmCouponInUse( $_code ) {
		if ( ! class_exists( DiscountHelper::class ) ) {
			return null;
		}
		$order_id = isset( $_REQUEST['virtuemart_order_id'] ) ? $_REQUEST['virtuemart_order_id'] : 0;
		return DiscountHelper::instance()->order_new( $order_id );
	}

	public function plgVmCouponHandler( $_code, & $_cartData, & $_cartPrices ) {
		if ( ! class_exists( DiscountHelper::class ) ) {
			return null;
		}
		return DiscountHelper::instance()->process_coupon_code( $_code, $_cartData, $_cartPrices );
	}

	public function plgVmUpdateTotals( & $_cartData, & $_cartPrices ) {
		if ( ! class_exists( DiscountHelper::class ) ) {
			return null;
		}
		return DiscountHelper::instance()->cart_calculate_totals( $_cartData, $_cartPrices );
	}


	private function check_coupon_new() {
		if ( version_compare( DiscountHelper::instance()->vmversion, '4.6.0', '<' ) ) {
			return;
		}
		$task = JFactory::getApplication()->input->get( 'task' ); 
		$coupon_code = JFactory::getApplication()->input->get( 'coupon_code' ); 
		if ( $task != 'updatecart' || empty( $coupon_code ) ) {
			return;
		}

		DiscountHelper::instance()->setCouponCode( $coupon_code );
		JFactory::getApplication()->redirect( JRoute::_( 'index.php?option=com_virtuemart&view=cart&Itemid=' . (int) JFactory::getApplication()->input->get( 'Itemid' ) ) );
	}

	private function check_coupon_delete() {
		$task1 = JFactory::getApplication()->input->get( 'task' ); 
		$task2 = JFactory::getApplication()->input->get( 'task2' ); 
		if ( $task1 != 'deletecoupons' && $task2 != 'deletecoupons' ) {
			return;
		}

		DiscountHelper::instance()->delete_code_from_cart();
		JFactory::getApplication()->redirect( JRoute::_( 'index.php?option=com_virtuemart&view=cart&Itemid=' . (int) JFactory::getApplication()->input->get( 'Itemid' ) ) );
	}





}

