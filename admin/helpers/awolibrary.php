<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awocouponModel.php';
require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awocouponView.php';

class awoLibrary {
	public static function dbEscape($value,$extra=false) {
		$db = JFactory::getDBO();
		if(version_compare( JVERSION, '1.6.0', 'ge' )) $value = $db->escape($value,$extra);
		else $value = $db->getEscaped($value,$extra);
		
		return $value;
	}

}


if (!function_exists('printr')) { function printr($a) { echo '<pre>'.print_r($a,1).'</pre>'; } }
if (!function_exists('printrx')) { function printrx($a) { echo '<pre>'.print_r($a,1).'</pre>'; exit; } }
if (!function_exists('awotrace')) {
	function awotrace() {
		$data = debug_backtrace();
		$rtn = array();
		foreach($data as $r) $rtn[] = @$r['file'].':'.@$r['line'].' function '.@$r['function'];
		return array_reverse($rtn);
	}
}
