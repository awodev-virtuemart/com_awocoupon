<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
 function printr($a) { echo '<pre>'.print_r($a,1).'</pre>'; }
 function printrx($a) { echo '<pre>'.print_r($a,1).'</pre>'; exit; }
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

// Set the table directory
JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');

global $mainframe;

//Set filepath
define('com_awocoupon_ASSETS',    		JURI::base().'components/com_awocoupon/assets');
define('com_awocoupon_ASSETS_SITE',    	$mainframe->getSiteURL().'components/com_awocoupon/assets');

// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');

//Create the controller
$classname  = 'AwoCouponController';
$controller = new $classname( );

// Perform the Request task
$controller->execute( JRequest::getWord('task', ''));
$controller->redirect();
?>
<br><div align="right" style="font-size:9px;">&copy;<?php echo date('Y');?> <a href="http://awodev.com" target="_blank">AwoCoupon</a> by Seyi Awofadeju <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GNU/GPL License</a></div>
