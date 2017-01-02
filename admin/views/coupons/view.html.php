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

class AwoCouponViewCoupons extends JView {

	function display($tpl = null) {
		global $mainframe, $option;
		
		//initialise variables
		$db  		= & JFactory::getDBO();
		$document	= & JFactory::getDocument();
		
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.modal');

		//get vars
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.coupons.filter_order', 	'filter_order', 	'c.coupon_code', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.coupons.filter_order_Dir',	'filter_order_Dir',	'', 'word' );
		$filter_state 	= $mainframe->getUserStateFromRequest( $option.'.coupons.filter_state', 	'filter_state', 	'', 'cmd' );
		$filter_coupon_value_type = $mainframe->getUserStateFromRequest( $option.'.coupons.filter_coupon_value_type', 		'filter_coupon_value_type', 		'', 'cmd' );
		$filter_discount_type = $mainframe->getUserStateFromRequest( $option.'.coupons.filter_discount_type', 		'filter_discount_type', 		'', 'cmd' );
		$filter_function_type = $mainframe->getUserStateFromRequest( $option.'.coupons.filter_function_type', 		'filter_function_type', 		'', 'cmd' );
		$filter_expiration = $mainframe->getUserStateFromRequest( $option.'.coupons.filter_expiration', 		'filter_expiration', 		'', 'cmd' );
		$search 			= $mainframe->getUserStateFromRequest( $option.'.coupons.search', 			'search', 			'', 'string' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );

		//add css and submenu to document
		$document->addStyleSheet('components/com_awocoupon/assets/css/style.css');

		//create the toolbar
		JToolBarHelper::title( JText::_( 'COUPONS' ), 'coupons' );
		JToolBarHelper::publishList('publishcoupon');
		JToolBarHelper::unpublishList('unpublishcoupon');
		JToolBarHelper::divider();
		JToolBarHelper::addNew('addcoupon');
		JToolBarHelper::editList('editcoupon');
		JToolBarHelper::divider();
		JToolBarHelper::deleteList( JText::_( 'ARE YOU SURE YOU WANT TO DELETE THE COUPONS' ),'removecoupon');
		JToolBarHelper::spacer();

		//Get data from the model
		$rows      	= & $this->get( 'Data');
		$pageNav 	= & $this->get( 'Pagination' );

		// build the html for published		
		$tmp = array();
		$tmp[] = JHTML::_('select.option',  '', ' - '.JText::_( 'SELECT STATE' ).' - ' );
		$tmp[] = JHTML::_('select.option',  '1', JText::_( 'ACTIVE' ) );
		$tmp[] = JHTML::_('select.option',  '-1', JText::_( 'INACTIVE' ) );
		$lists['published'] = JHTML::_('select.genericlist', $tmp, 'filter_state', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_state );		

		$tmp = array();
		$tmp[] = JHTML::_('select.option',  '', ' - '.JText::_( 'SELECT PERCENT OR TOTAL' ).' - ' );
		$tmp[] = JHTML::_('select.option',  'percent', JText::_( 'PERCENTAGE' ) );
		$tmp[] = JHTML::_('select.option',  'total', JText::_( 'TOTAL' ) );
		$lists['coupon_value_type'] = JHTML::_('select.genericlist', $tmp, 'filter_coupon_value_type', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_coupon_value_type );		

		$tmp = array();
		$tmp[] = JHTML::_('select.option',  '', ' - '.JText::_( 'SELECT DISCOUNT TYPE' ).' - ' );
		$tmp[] = JHTML::_('select.option',  'overall', JText::_( 'OVERALL' ) );
		$tmp[] = JHTML::_('select.option',  'specific', JText::_( 'SPECIFIC' ) );
		$lists['discount_type'] = JHTML::_('select.genericlist', $tmp, 'filter_discount_type', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_discount_type );		
		
		// search filter
		$lists['search']= $search;

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		//assign data to template
		$this->assignRef('lists'      	, $lists);
		$this->assignRef('rows'      	, $rows);
		$this->assignRef('pageNav' 		, $pageNav);
		$this->assignRef('ordering'		, $ordering);

		parent::display($tpl);
	}
}
?>