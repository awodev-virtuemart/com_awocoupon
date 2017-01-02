<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// check if logged in

require JPATH_COMPONENT_ADMINISTRATOR.'/awocoupon.config.php';

$jlang = JFactory::getLanguage();
$jlang->load('com_awocoupon', JPATH_ADMINISTRATOR, 'en-GB', true);
$jlang->load('com_awocoupon', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
$jlang->load('com_awocoupon', JPATH_ADMINISTRATOR, null, true);
$jlang->load('com_awocoupon', JPATH_SITE, 'en-GB', true);
$jlang->load('com_awocoupon', JPATH_SITE, $jlang->getDefault(), true);
$jlang->load('com_awocoupon', JPATH_SITE, null, true);


require_once JPATH_COMPONENT.'/controller.php';

$controller = new AwoCouponSiteController( );
$controller->registerTask( 'results', 'display' );
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
