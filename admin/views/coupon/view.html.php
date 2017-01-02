<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');


class AwoCouponViewCoupon extends AwoCouponView {

	function display($tpl = null) {

	
		global $def_lists;

		parent::display_beforeload();

		//initialise variables
		$document	= JFactory::getDocument();
		$cid 		= JRequest::getVar( 'cid' );
		$lists 		= array();
		
		
		
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.calendar');
		jimport('joomla.html.pane');
		
		
		//add css/ & js to document
		$document->addStyleSheet(com_awocoupon_ASSETS.'/css/jquery-ui.css');
		$document->addScript(com_awocoupon_ASSETS.'/js/jquery-ui.min.js');
		$document->addScript(com_awocoupon_ASSETS.'/js/jquery.ui.autocomplete.ext.js');

		
		//create the toolbar
		if ( $cid ) JToolBarHelper::title( JText::_( 'COM_AWOCOUPON_CP_COUPON' ), 'edit' );
		else JToolBarHelper::title( JText::_( 'COM_AWOCOUPON_CP_COUPON' ), 'new' );

		JToolBarHelper::save('savecoupon');
		JToolBarHelper::divider();
		JToolBarHelper::cancel('cancelcoupon');
		JToolBarHelper::spacer();

		// fail if checked out not by 'me'
		$row     		= $this->get( 'Entry' );
		

		$post = JRequest::get('post');
		if ( $post ) {
			if(!empty($post['userlist'])) {
				$tmp = $post['userlist'];
				$post['userlist'] = array();
				foreach($tmp as $id) $post['userlist'][$id] = (object) array('user_id'=>$id,'user_name'=>$post['usernamelist'][$id]);
			} //else $post['productlist'] = array();
			if(!empty($post['assetlist'])) {
				$tmp = $post['assetlist'];
				$post['assetlist'] = array();
				foreach($tmp as $id) $post['assetlist'][$id] = (object) array('asset_id'=>$id,'asset_name'=>$post['assetnamelist'][$id]);
			} 
			$row = (object) array_merge((array) $row, (array) $post); //bind the db return and post
			//$row->bind($post);
		}

		// build the html for select boxes

		$states=array();
		foreach($def_lists['published'] as $key=>$value) $states[] = JHTML::_('select.option', $key, $value);
		$lists['published'] = JHTML::_('select.genericlist', $states, 'published', 'class="inputbox" style="width:147px;"', 'value', 'text', $row->published );		
		$states=array();
		foreach($def_lists['coupon_value_type'] as $key=>$value) $states[] = JHTML::_('select.option', $key, $value);
		$lists['coupon_value_type'] = JHTML::_('select.genericlist', $states, 'coupon_value_type', 'class="inputbox" style="width:147px;"', 'value', 'text', $row->coupon_value_type );		
		$states=array();
		foreach($def_lists['discount_type'] as $key=>$value) $states[] = JHTML::_('select.option', $key, $value);
		$lists['discount_type'] = JHTML::_('select.genericlist', $states, 'discount_type', 'class="inputbox" style="width:147px;"', 'value', 'text', $row->discount_type );		
		$states=array();
		$states[] = JHTML::_('select.option', '', '');
		foreach($def_lists['num_of_uses_type'] as $key=>$value) $states[] = JHTML::_('select.option', $key, $value);
		$lists['num_of_uses_type'] = JHTML::_('select.genericlist', $states, 'num_of_uses_type', 'class="inputbox" style="width:100px;"', 'value', 'text', $row->num_of_uses_type );		
		
		$states = array(JHTML::_('select.option', '', ''),
						JHTML::_('select.option', 'product', JText::_( 'COM_AWOCOUPON_CP_PRODUCT' )),
						JHTML::_('select.option', 'category', JText::_( 'COM_AWOCOUPON_CP_CATEGORY' )),
					);
		$lists['asset1_function_type'] = JHTML::_('select.genericlist', $states, 'asset1_function_type', 'class="inputbox" style="width:147px;" onchange="asset_type_change();"', 'value', 'text', $row->asset1_function_type );		
		
		//assign data to template
		$this->assignRef('lists'      			, $lists);
		$this->assignRef('row'      			, $row);
		$this->assignRef('def_lists'			, $def_lists);

		parent::display($tpl);
	}
	
}

