<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponViewAssets extends AwoCouponView {

	function display($tpl = null) {
		global $def_lists;
		
		parent::display_beforeload();

		$app = JFactory::getApplication(); 
		$templateDir = JURI::base() . 'templates/' . $app->getTemplate(); 

		//create the toolbar
		JRequest::setVar('tmpl', 'component');
		$document	= JFactory::getDocument();
		$document->addStyleSheet($templateDir.'/css/template.css');
		$document->addStyleSheet('templates/system/css/system.css');

		//initialise variables
		$cid 		= JRequest::getVar( 'cid' );
		
		//get vars
		$filter_order		= JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.assets.filter_order', 	'filter_order', 	'asset_name', 'cmd' );
		$filter_order_Dir	= JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.assets.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		
		//Get data from the model
		$row      	= $this->get( 'Data');
		
		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		//assign data to template
		$this->assignRef('lists'      	, $lists);
		$this->assignRef('row'      	, $row);
		$this->assignRef('ordering'		, $ordering);
		$this->assignRef('now'			, $now);
		$this->assignRef('def_lists', $def_lists);

		parent::display($tpl);
	}
}
