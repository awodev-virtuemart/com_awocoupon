<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class TableCoupons extends JTable {
	var $id					= null;
	var $coupon_code				= null;
	var $num_of_uses			= null;
	var $coupon_value_type			= null;
	var $coupon_value			= null;
	var $min_value			= null;
	var $discount_type		= null;
	var $function_type		= null;
	var $expiration			= null;
	var $published			= null;

	
	/**
	* @param database A database connector object
	*/
	function __construct( &$db ) {
		parent::__construct( '#__awocoupon', 'id', $db );
	}

	/**
	 * Overloaded check function
	 **/
	function check() {
		$err = '';
		if(empty($this->coupon_code)) $err .= JText::_('PLEASE ENTER A COUPON CODE');
		if(empty($this->coupon_value_type) || ($this->coupon_value_type!='percent' && $this->coupon_value_type!='total')) $err .= JText::_('PLEASE ENTER A VALUE TYPE');
		if(empty($this->coupon_value) || !is_numeric($this->coupon_value)) $err .= JText::_('PLEASE ENTER A VALID VALUE');
		if(empty($this->discount_type) || ($this->discount_type!='specific' && $this->discount_type!='overall')) $err .= JText::_('PLEASE ENTER A VALID DISCOUNT TYPE');
		if(empty($this->function_type) || ($this->function_type!='coupon' && $this->function_type!='giftcert')) $err .= JText::_('PLEASE ENTER A VALID FUNCTION TYPE');

		if(!empty($err)) {
			$this->setError($err);
			return false;
		}

		return true;
	}
}
