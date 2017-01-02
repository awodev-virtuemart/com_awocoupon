<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

if( ! defined( '_VALID_MOS' ) && ! defined( '_JEXEC' ) ) die( 'Direct Access to ' . basename( __FILE__ ) . ' is not allowed.' ) ;


class plgVmCouponAwoCoupon extends JPlugin {

	public function __construct(& $subject, $config){
		parent::__construct($subject, $config);
	}

	public function onAfterDispatch() {
		$app = JFactory::getApplication();
		if ($app->isAdmin()) return; 
	  
		$option = JRequest::getCmd('option'); 
		if($option!='com_virtuemart') return;
		
		$task1 = JRequest::getCmd('task');
		$task2 = JRequest::getCmd('task2');
		if($task1!='deletecoupons' && $task2!='deletecoupons') return;
				
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/vm_coupon.php';
		if(!file_exists($awo_file)) return;
		
		if(!class_exists('vm_coupon')) require $awo_file;
		vm_coupon::delete_code_from_cart();
		
		$app->redirect('index.php?option=com_virtuemart&view=cart&Itemid='.JRequest::getInt('Itemid'));
		
		return;
		
	}

	function plgVmValidateCouponCode($_code,$_billTotal) {
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/vm_coupon.php';
		if(!file_exists($awo_file)) return null;
		
		if(!class_exists('vm_coupon')) require $awo_file;
		$vm_coupon = new vm_coupon();
		return $vm_coupon->vm_ValidateCouponCode($_code);
	}

	function plgVmRemoveCoupon($_code,$_force) {
		return $this->plgVmCouponInUse($_code);
	}
	
	
	function plgVmCouponInUse($_code) {
	
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/vm_coupon.php';
		if( ! file_exists($awo_file)) return null;
				
		if(!class_exists('vm_coupon')) require $awo_file;
		return vm_coupon::remove_coupon_code($_code);
		
	}
	
	
	function plgVmCouponHandler($_code, & $_cartData, & $_cartPrices) {
		$awo_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/vm_coupon.php';
		if( ! file_exists($awo_file)) return null;
			
		if(!class_exists('vm_coupon')) require $awo_file;
		return vm_coupon::process_coupon_code($_code, $_cartData, $_cartPrices );
	}
	
}

// No closing tag