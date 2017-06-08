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

	public static function getDate($date=null, $format=null, $type='utc2loc') {
		
		if(version_compare(JVERSION,'1.6.0','ge')) {
			if(empty($date)) $date = 'now';
		}
		else {
			if(empty($date)) $date = time();
			$d1 = stristr(PHP_OS,"win") ? '%#d' : '%e';
			$format = str_replace(
				array( 'M',  'Y', 'm', 'n', 'd', 'j', 'H', 'i', 's' ,'F', 'y', 'l', 'D',),
				array('%b', '%Y','%m','%m','%d', $d1,'%H','%M','%S','%B','%y','%A','%a',),
				$format
			);
		}
		
		if(is_numeric($date)) $date = gmdate('c', $date);

		if(in_array($type, array('utc2loc','utc2utc'))) {
			if($type=='utc2loc') {
				$tz = true;
				$offset = null;
			}
			elseif($type=='utc2utc') {
				$tz = null;
				$offset = 0;
			}
			return version_compare( JVERSION, '1.6.0', 'ge' )
				? JHTML::_('date',$date,$format,$tz)
				: JHTML::_('date',strtotime($date),$format,$offset)
			;
		}
		elseif($type=='loc2utc') {
			$local = false;
			return version_compare( JVERSION, '1.6.0', 'ge' ) 
				? JFactory::getDate($date,JFactory::getConfig()->get('offset'))->format($format,$local)
				: JFactory::getDate($date,JFactory::getConfig()->getValue ( 'offset' )*1)->toFormat($format)
			;
		}
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
