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

class AwoCouponViewCoupon extends JView {

	function display($tpl = null) {
	
		global $mainframe;

		//Load pane behavior
		jimport('joomla.html.pane');

		//initialise variables
		$document	= & JFactory::getDocument();
		$cid 		= JRequest::getVar( 'cid' );
		$lists 		= array();
		
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.calendar');
		
		//add css/ & js to document
		$document->addStyleSheet('components/com_awocoupon/assets/css/style.css');
		
		//create the toolbar
		if ( $cid ) JToolBarHelper::title( JText::_( 'EDIT COUPON' ), 'edit' );
		else JToolBarHelper::title( JText::_( 'NEW COUPON' ), 'new' );

		JToolBarHelper::save('savecoupon');
		JToolBarHelper::divider();
		JToolBarHelper::cancel('cancelcoupon');
		JToolBarHelper::spacer();

		// fail if checked out not by 'me'
		$model			= & $this->getModel();
		$userlist     	= & $this->get( 'UserList' );
		$productlist     	= & $this->get( 'ProductList' );
		$row     		= & $this->get( 'Entry' );
		
		$post = JRequest::get('post');
		if ( $post ) {
			if(!empty($post['userlist'])) {
				$tmp = $post['userlist'];
				$post['userlist'] = array();
				foreach($tmp as $tmp2) $post['userlist'][$tmp2] = $tmp2;
			}
			if(!empty($post['productlist'])) {
				$tmp = $post['productlist'];
				$post['productlist'] = array();
				foreach($tmp as $tmp2) $post['productlist'][$tmp2] = $tmp2;
			}
			$row = (object) array_merge((array) $row, (array) $post); //bind the db return and post
			//$row->bind($post);
		}

		// build the html for select boxes
		$states=array();
		$states[] = JHTML::_('select.option',  '1', JText::_( 'PUBLISHED' ) );
		$states[] = JHTML::_('select.option',  '-1', JText::_( 'UNPUBLISHED' ) );
		$lists['published'] = JHTML::_('select.genericlist', $states, 'published', 'class="inputbox" size="1"', 'value', 'text', $row->published );		
		$states=array();
		$states[] = JHTML::_('select.option', 'percent', JText::_('PERCENTAGE'));
		$states[] = JHTML::_('select.option', 'total', JText::_('TOTAL'));
		$lists['coupon_value_type'] = JHTML::_('select.genericlist', $states, 'coupon_value_type', 'class="inputbox"', 'value', 'text', $row->coupon_value_type );		
		$states=array();
		$states[] = JHTML::_('select.option', 'overall', JText::_('OVERALL'));
		$states[] = JHTML::_('select.option', 'specific', JText::_('SPECIFIC'));
		$lists['discount_type'] = JHTML::_('select.genericlist', $states, 'discount_type', 'class="inputbox"', 'value', 'text', $row->discount_type );		
		$states=array();
		$states[] = JHTML::_('select.option', '', '');
		$states[] = JHTML::_('select.option', 'total', JText::_('TOTAL'));
		$states[] = JHTML::_('select.option', 'per_user', JText::_('PER CUSTOMER'));
		$lists['num_of_uses_type'] = JHTML::_('select.genericlist', $states, 'num_of_uses_type', 'class="inputbox"', 'value', 'text', $row->num_of_uses_type );		
		
		
		//assign data to template
		$this->assignRef('lists'      			, $lists);
		$this->assignRef('row'      			, $row);
		$this->assignRef('userlist'    			, $userlist);
		$this->assignRef('productlist'			, $productlist);

		parent::display($tpl);
	}
}
?>