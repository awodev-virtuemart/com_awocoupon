<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');


class AwoCouponViewInstallation extends AwoCouponView {
	/**
	 * Creates the Entrypage
	 *
	 * @since 1.0
	 */
	function display( $tpl = null ) {

		parent::display_beforeload();

		//initialise variables
		$document	= JFactory::getDocument();

		//build toolbar
		JToolBarHelper::title( JText::_('COM_AWOCOUPON_FI_INSTALLATION_CHECK'), 'installation' );
		JToolBarHelper::publishList('publishplugin');
		JToolBarHelper::unpublishList('unpublishplugin');
		
		//Get data from the model
		$rows      	= $this->get( 'Data');
		$pageNav 	= $this->get( 'Pagination' );
		


		$this->assignRef('rows'      	, $rows);
		$this->assignRef('pageNav' 		, $pageNav);

		parent::display($tpl);

	}
	
}