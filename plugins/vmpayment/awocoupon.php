<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

if (!class_exists ('vmPSPlugin'))  require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');

class plgVmPaymentAwoCoupon extends vmPSPlugin {

	public function __construct(& $subject, $config) { parent::__construct ($subject, $config); }

	
	public function plgVmgetPaymentCurrency($virtuemart_paymentmethod_id, &$paymentCurrency){
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/vm_coupon.php';
		if(file_exists($awo_file)) {
			if(!class_exists('VirtueMartCart')) require JPATH_ROOT.'/components/com_virtuemart/helpers/cart.php';
			$cart = VirtueMartCart::getCart();
			if(empty($cart)) return null;

			$session = JFactory::getSession();
			$awosess = $session->get('coupon', '', 'awocoupon');
			if(empty($awosess) ) {
			# removes error message from cart if coupon does not exist
				$cart->couponCode = '';
				$cart->setCartIntoSession();
			}

		}
		
		return null;
	}

}
