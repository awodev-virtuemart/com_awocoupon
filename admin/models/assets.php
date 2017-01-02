<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponModelAssets extends AwoCouponModel {

	public function __construct() {
		$this->_type = 'coupons';
		parent::__construct();

		$id		= JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.assets.id', 	'id', 	JRequest::getVar( 'id' ), 'cmd' );
		$this->setId($id);

	}

	/**
	 * Method to get data
	 **/
	function getData() {
		// Lets load the files if it doesn't already exist
		if (empty($this->_data)) {

			$controller = new AwoCouponController( );
			$model_coupon = $controller->getModel('coupon');
			$model_coupon->setId($this->_id);
			$rows      	= $model_coupon->getEntry();

			$query = $this->_buildQuery($rows);
			$rows->assets = $this->_getList($query);

			$this->_data = $rows;

		}
			
		return $this->_data;
	}

	
	/**
	 * Method to build the query
	 **/
	function _buildQuery($row) {
		

		// Get the WHERE, and ORDER BY clauses for the query
		$orderby	= $this->_buildContentOrderBy();

		$sql = '';
		if($row->function_type2=='product') {
			$sql = 'SELECT c.id,c.coupon_code,c.num_of_uses,c.coupon_value_type,c.coupon_value,
						 c.min_value,c.discount_type,c.function_type,c.expiration,pv.virtuemart_product_id as asset_id,lang.product_name as asset_name
					 FROM #__'.AWOCOUPON.' c
					 JOIN #__'.AWOCOUPON.'_product p ON p.coupon_id=c.id
					 JOIN #__virtuemart_products pv ON pv.virtuemart_product_id=p.product_id
					 JOIN `#__virtuemart_products_'.VMLANG.'` as lang using (`virtuemart_product_id`)
					WHERE c.id='.$this->_id.'
					'.$orderby
				;
		}
		elseif($row->function_type2=='category') {
			$sql = 'SELECT c.id,c.coupon_code,c.num_of_uses,c.coupon_value_type,c.coupon_value,
						 c.min_value,c.discount_type,c.function_type,c.expiration,
						 pv.virtuemart_category_id as asset_id,lang.category_name as asset_name
					 FROM #__'.AWOCOUPON.' c
					 JOIN #__'.AWOCOUPON.'_category p ON p.coupon_id=c.id
					 JOIN #__virtuemart_categories pv ON pv.virtuemart_category_id=p.category_id
					 JOIN `#__virtuemart_categories_'.VMLANG.'` as lang using (`virtuemart_category_id`)
					WHERE c.id='.$this->_id.'
					'.$orderby
				;
		}
		return $sql;
	}

	/**
	 * Method to build the orderby clause of the query
	 **/
	function _buildContentOrderBy() {

		$filter_order		= JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.assets.filter_order', 	'filter_order', 	'asset_name', 'cmd' );
		$filter_order_Dir	= JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.assets.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby 	= !empty($filter_order) ? ' ORDER BY '.$filter_order.' '.$filter_order_Dir : '';

		return $orderby;
	}

	
	function deleteassets($cids) {	
		$total = count($cids);
		$cids = implode( '\',\'', $cids );

		$sql = 'DELETE FROM #__'.AWOCOUPON.'_product WHERE coupon_id='.(int)$this->_id.' AND product_id IN (\''. $cids .'\')';
		$this->_db->setQuery( $sql );
		if(!$this->_db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return $this->_db->getAffectedRows().' '.JText::_('COM_AWOCOUPON_MSG_ITEMS_DELETED');
	}
	

}
