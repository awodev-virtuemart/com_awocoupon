<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');


class AwoCouponViewCoupons extends AwoCouponView {

	function display($tpl = null) {
		global $def_lists;

		parent::display_beforeload();

		//initialise variables
		$db  		= JFactory::getDBO();
		$document	= JFactory::getDocument();
		
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.modal');

		//get vars
		$filter_order		= JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.coupons.filter_order', 	'filter_order', 	'c.coupon_code', 'cmd' );
		$filter_order_Dir	= JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.coupons.filter_order_Dir',	'filter_order_Dir',	'', 'word' );
		$filter_state 	= JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.coupons.filter_state', 	'filter_state', 	'', 'cmd' );
		$filter_coupon_value_type = JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.coupons.filter_coupon_value_type', 		'filter_coupon_value_type', 		'', 'cmd' );
		$filter_discount_type = JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.coupons.filter_discount_type', 		'filter_discount_type', 		'', 'cmd' );
		$filter_function_type = JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.coupons.filter_function_type', 		'filter_function_type', 		'', 'cmd' );
		$filter_expiration = JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.coupons.filter_expiration', 		'filter_expiration', 		'', 'cmd' );
		$search 			= JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.coupons.search', 			'search', 			'', 'string' );
		$search 			= awolibrary::dbescape( trim(JString::strtolower( $search ) ) );

		//create the toolbar
		JToolBarHelper::title( JText::_( 'COM_AWOCOUPON_CP_COUPONS' ), 'coupons' );
		JToolBarHelper::publishList('publishcoupon');
		JToolBarHelper::unpublishList('unpublishcoupon');
		JToolBarHelper::divider();
		JToolBarHelper::addNew('addcoupon');
		JToolBarHelper::editList('editcoupon');
		JToolBarHelper::divider();
		JToolBarHelper::deleteList( JText::_( 'COM_AWOCOUPON_ERR_CONFIRM_DELETE' ),'removecoupon');
		JToolBarHelper::spacer();

		//Get data from the model
		$rows      	= $this->get( 'Data');
		$pageNav 	= $this->get( 'Pagination' );

		// build the html for published		
		$tmp = array();
		$tmp[] = JHTML::_('select.option',  '', ' - '.JText::_( 'COM_AWOCOUPON_SELECT_STATUS' ).' - ' );
		foreach($def_lists['published'] as $key=>$value) $tmp[] = JHTML::_('select.option', $key, $value);
		$lists['published'] = JHTML::_('select.genericlist', $tmp, 'filter_state', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_state );		

		$tmp = array();
		$tmp[] = JHTML::_('select.option',  '', ' - '.JText::_( 'COM_AWOCOUPON_SELECT_PERCENT_AMOUNT' ).' - ' );
		foreach($def_lists['coupon_value_type'] as $key=>$value) $tmp[] = JHTML::_('select.option', $key, $value);
		$lists['coupon_value_type'] = JHTML::_('select.genericlist', $tmp, 'filter_coupon_value_type', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_coupon_value_type );		

		$tmp = array();
		$tmp[] = JHTML::_('select.option',  '', ' - '.JText::_( 'COM_AWOCOUPON_SELECT_DISCOUNT_TYPE' ).' - ' );
		foreach($def_lists['discount_type'] as $key=>$value) $tmp[] = JHTML::_('select.option', $key, $value);
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
		$this->assignRef('def_lists', $def_lists);

		parent::display($tpl);
	}
}
