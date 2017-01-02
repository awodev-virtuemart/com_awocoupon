<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponViewUsers extends AwoCouponView {

	function display($tpl = null) {
		global $def_lists;
		
		parent::display_beforeload();

		$app = JFactory::getApplication(); 
		$templateDir = JURI::base() . 'templates/' . $app->getTemplate(); 

		//create the toolbar
		JRequest::setVar('tmpl', 'component');
		$document	= JFactory::getDocument();
		$document->setTitle( JText::_('COM_AWOCOUPON_CP_CUSTOMERS') );
		$document->addStyleSheet($templateDir.'/css/template.css');
		$document->addStyleSheet('templates/system/css/system.css');

		//initialise variables
		$cid 		= JRequest::getVar( 'cid' );
		
		//get vars
		$filter_order		= JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.users.filter_order', 	'filter_order', 	'c.coupon_code', 'cmd' );
		$filter_order_Dir	= JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.users.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		//Get data from the model
		$row      	= $this->get( 'Data');
		$pageNav 	= $this->get( 'Pagination' );
		
		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		$user_name = $user_title = '';
		$user_name = 'USER'; $user_title = 'COM_AWOCOUPON_CP_CUSTOMERS';

		//assign data to template
		$this->assignRef('id'      	, $id);
		$this->assignRef('lists'      	, $lists);
		$this->assignRef('row'      	, $row);
		$this->assignRef('user_name'      , $user_name);
		$this->assignRef('user_title'      , $user_title);
		$this->assignRef('pageNav' 		, $pageNav);
		$this->assignRef('ordering'		, $ordering);
		$this->assignRef('def_lists', $def_lists);

		parent::display($tpl);
	}
}
