<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponTableCoupons extends JTable {
	
	var $id					= null;
	var $coupon_code				= null;
	var $num_of_uses			= null;
	var $coupon_value_type			= null;
	var $coupon_value			= null;
	var $min_value			= null;
	var $discount_type		= null;
	var $function_type		= null;
	var $function_type2		= null;
	var $startdate				= null;
	var $expiration			= null;
	var $published			= null;

	
	/**
	* @param database A database connector object
	*/
	public function __construct( &$db ) {
		parent::__construct( '#__'.AWOCOUPON, 'id', $db );
	}

	/**
	 * Overloaded check function
	 **/
	function check() {
		$err = array();
		
		
		if(empty($this->coupon_code)) $err[] = JText::_('COM_AWOCOUPON_CP_COUPON').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
		if(empty($this->coupon_value_type) || ($this->coupon_value_type!='percent' && $this->coupon_value_type!='total')) $err[] = JText::_('COM_AWOCOUPON_CP_VALUE_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
		if(empty($this->coupon_value) || !is_numeric($this->coupon_value)) $err[] = JText::_('COM_AWOCOUPON_CP_VALUE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
		if(empty($this->discount_type) || ($this->discount_type!='specific' && $this->discount_type!='overall')) $err[] = JText::_('COM_AWOCOUPON_CP_DISCOUNT_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
		if(empty($this->function_type) || ($this->function_type!='coupon' && $this->function_type!='giftcert')) $err[] = JText::_('COM_AWOCOUPON_CP_FUNCTION_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
		if(!empty($this->function_type2) && ($this->function_type2!='product' && $this->function_type2!='category')) $err[] = JText::_('COM_AWOCOUPON_CP_ASSET').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');

		$is_start = true;
		if(!empty($this->startdate)) {
			if(!preg_match("/^\d{4}\-\d{2}\-\d{2}$/",$this->startdate)) {
				$is_start = false;
				$err[] = JText::_('COM_AWOCOUPON_CP_DATE_START').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
			}
			else {
				list($Y,$M,$D) = explode('-',$this->startdate);
				if($Y>2100 || $M>12 || $D>31) {
					$is_start = false;
					$err[] = JText::_('COM_AWOCOUPON_CP_DATE_START').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				}
			}
		} else $is_start = false;
		$is_end = true;
		if(!empty($this->expiration)) {
			if(!preg_match("/^\d{4}\-\d{2}\-\d{2}$/",$this->expiration)) {
				$is_end = true;
				$err[] = JText::_('COM_AWOCOUPON_CP_EXPIRATION').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
			}
			else {
				list($Y,$M,$D) = explode('-',$this->expiration);
				if($Y>2100 || $M>12 || $D>31) {
					$is_end = true;
					$err[] = JText::_('COM_AWOCOUPON_CP_EXPIRATION').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				}
			}
		} else $is_end = false;
		if($is_start && $is_end) {
			$c1 = (int)str_replace('-','',$this->startdate);
			$c2 = (int)str_replace('-','',$this->expiration);
			if($c1>$c2) $err[] = JText::_('COM_AWOCOUPON_CP_DATE_START').'/'.JText::_('COM_AWOCOUPON_CP_EXPIRATION').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
		}
		
		

		if(!empty($err)) {
			foreach($err as $error) JFactory::getApplication()->enqueueMessage($error, 'error');//$this->setError($error);
			return false;
		}

		return true;
	}
}
