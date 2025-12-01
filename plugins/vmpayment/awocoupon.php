<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory as JFactory;
use AwoDev\Component\AwoCoupon\Administrator\Helper\DiscountHelper;

if ( ! class_exists( 'vmPSPlugin' ) ) {
	require JPATH_VM_PLUGINS . '/vmpsplugin.php';
}

class plgVmPaymentAwoCoupon extends vmPSPlugin {

	public function plgVmgetPaymentCurrency( $virtuemart_paymentmethod_id, & $paymentCurrency ) {
		// call once per session
		static $is_called = false;
		if ( $is_called ) {
			return null;
		}
		$is_called = true;

		if ( ! class_exists( DiscountHelper::class ) ) {
			return null;
		}
		if ( JFactory::getApplication()->isClient( 'administrator' ) ) {
			return null;
		}			

		if ( ! class_exists( 'VirtueMartCart' ) ) {
			require JPATH_ROOT . '/components/com_virtuemart/helpers/cart.php';
		}
		$cart = VirtueMartCart::getCart();
		if ( empty( $cart ) ) {
			return null;
		}

		$awosess = JFactory::getSession()->get( 'coupon', '', 'awocoupon' );
		if ( empty( $awosess ) ) {
		# removes error message from cart if coupon does not exist
			$cart->couponCode = '';
			$cart->setCartIntoSession();
		}
	}

}

