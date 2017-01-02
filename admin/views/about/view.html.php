<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

class AwoCouponViewAbout extends AwoCouponView {

	function display( $tpl = null ) {
		parent::display_beforeload();

		// Load tooltips
		JHTML::_('behavior.tooltip', '.hasTip');

		//add css to document
		$document	= JFactory::getDocument();

		//create the toolbar
		JToolBarHelper::title( JText::_( 'COM_AWOCOUPON_AT_ABOUT' ), 'awocoupon' );

		//Retreive version from install file
		$element = simplexml_load_file(JPATH_ADMINISTRATOR.'/components/com_awocoupon/awocoupon'.(version_compare(JVERSION, '3.0.0', 'ge') ? '_j3':'').'.xml');
		$version = (string)$element->version;
		
		$this->assign( 'version'	, $version );
		
		parent::display( $tpl );
	}
}