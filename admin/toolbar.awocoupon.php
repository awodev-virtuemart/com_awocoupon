<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

$view	= JRequest::getCmd('view','');

JHTML::_('behavior.switcher');

// Load submenu's
$awocoupon_views	= array(
					array('', 				JText::_('COM_AWOCOUPON_DH_DASHBOARD')),
					array('coupons', 		JText::_('COM_AWOCOUPON_CP_COUPONS')),
					array('about',			JText::_('COM_AWOCOUPON_AT_ABOUT')),
				);	

if(version_compare( JVERSION, '3.0.0', 'ge' )) {
	$html_menu = '';
	foreach( $awocoupon_views as $key => $row ) {
		$active	= false;
		if(empty($row[0]) && !empty($view)) $row[0]='dashboard';
		if(empty($row[2])) $active = ( $view == $row[0] );
		else { foreach($row[2] as $ch) { if($view == $ch) { $active = true; break; } } }
		$key= $row[0]?'&view='.$row[0]:'';

		$html_menu .= '<li><a '.($active ? 'class="active"' : '').' href="index.php?option=com_awocoupon'.$key.'">'.$row[1].'</a>	</li>';
	}
	echo '<div id="submenu-box"><div class="m"><ul id="submenu">'.$html_menu.'	</ul><div class="clr"></div></div></div>';
}
else {
	foreach( $awocoupon_views as $key => $row ) {
		$active	= false;
		if(empty($row[2])) $active = ( $view == $row[0] );
		else { foreach($row[2] as $ch) { if($view == $ch) { $active = true; break; } } }
		$key= $row[0]?'&view='.$row[0]:'';
		JSubMenuHelper::addEntry( $row[1] , 'index.php?option=com_awocoupon'.$key , $active );
	}
}
