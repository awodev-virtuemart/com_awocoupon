<?php
/**
 * @component AwoCoupon
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if(version_compare( JVERSION, '3.0.0', 'ge' )) { class AwoCouponControllerConnect extends JControllerForm {} }
else {
	jimport('joomla.application.component.controller');
	class AwoCouponControllerConnect extends JController {}
}

class AwoCouponSiteController extends AwoCouponControllerConnect {
	/**
	 * Method to show the search view
	 *
	 * @access	public
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false) {
		JRequest::setVar('view', JRequest::getCmd('view', 'coupondelete'));
		parent::display();
	}

}
