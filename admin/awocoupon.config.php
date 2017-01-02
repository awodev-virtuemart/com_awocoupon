<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

//Set filepath
define('com_awocoupon_ASSETS',    				JURI::base().'components/com_awocoupon/assets');
define('AWOCOUPON',    							'awocoupon_vm');
define('AWOCOUPON_OPTION',    					'com_awocoupon');

//default values

//variables
$def_lists = array(
	'function_type' => array(
		'coupon'=>JText::_( 'COM_AWOCOUPON_CP_COUPON' ),
		'giftcert'=>JText::_( 'COM_AWOCOUPON_GC_GIFTCERT' ),
	),
	'function_type2' => array(
		'product'=>JText::_( 'COM_AWOCOUPON_CP_PRODUCTS' ),
		'category'=>JText::_( 'COM_AWOCOUPON_CP_CATEGORY' ),
	),
	'published' => array(
		'1'=>JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' ),
		'-1'=>JText::_( 'COM_AWOCOUPON_CP_UNPUBLISHED' ),
	),
	'coupon_value_type' => array(
		'percent'=>JText::_( 'COM_AWOCOUPON_CP_PERCENTAGE' ),
		'total'=>JText::_( 'COM_AWOCOUPON_CP_AMOUNT' ),
	),
	'discount_type' => array(
		'overall'=>JText::_( 'COM_AWOCOUPON_CP_OVERALL' ),
		'specific'=>JText::_( 'COM_AWOCOUPON_CP_SPECIFIC' ),
	),
	'num_of_uses_type' => array(
		'total'=>JText::_( 'COM_AWOCOUPON_GBL_TOTAL' ),
		'per_user'=>JText::_( 'COM_AWOCOUPON_CP_PER_CUSTOMER' ),
	),	
	
);

