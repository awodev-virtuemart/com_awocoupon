<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class AwoCouponViewProducts extends JView {

	function display($tpl = null) {
		global $mainframe, $option;
		
		$app =& JFactory::getApplication(); 
		$templateDir = JURI::base() . 'templates/' . $app->getTemplate(); 

		//create the toolbar
		JRequest::setVar('tmpl', 'component');
		$document	= & JFactory::getDocument();
		$document->setTitle( JText::_('USERS') );
		$document->addStyleSheet('components/com_awocoupon/assets/css/style.css');
		$document->addStyleSheet($templateDir.'/css/template.css');
		$document->addStyleSheet('templates/system/css/system.css');

		//initialise variables
		$cid 		= JRequest::getVar( 'cid' );
		
		//get vars
		$id		= $mainframe->getUserStateFromRequest( $option.'.products.id', 	'id', 	JRequest::getVar( 'id' ), 'cmd' );
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.products.filter_order', 	'filter_order', 	'c.coupon_code', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.products.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		//Get data from the model
		$row      	= & $this->get( 'Data');
		$pageNav 	= & $this->get( 'Pagination' );
		
		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		//assign data to template
		$this->assignRef('id'      	, $id);
		$this->assignRef('lists'      	, $lists);
		$this->assignRef('row'      	, $row);
		$this->assignRef('pageNav' 		, $pageNav);

		parent::display($tpl);
	}
}
?>