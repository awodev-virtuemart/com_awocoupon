<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

require_once JPATH_ADMINISTRATOR.DS."components".DS."com_awocoupon".DS.'helpers'.DS."vm_coupon.php";
return ps_coupon_remove::remove_coupon_code($d);


