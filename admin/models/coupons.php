<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponModelCoupons extends AwoCouponModel {
	public function __construct() {
		$this->_type = 'coupons';
		parent::__construct();

	}


	function getData() {
		// Lets load the files if it doesn't already exist
		if (empty($this->_data)) {
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			
			
			$ids = '';
			$ptr = null;
			foreach($this->_data as $i=>$row) {
				$ids .= $row->id.',';
				$ptr[$row->id]['usercount'] = &$this->_data[$i]->usercount;
				$ptr[$row->id]['assetcount'] = &$this->_data[$i]->assetcount;
				
				$this->_data[$i]->num_of_uses_type = '';
				if(!empty($this->_data[$i]->num_of_uses)) {
					$this->_data[$i]->num_of_uses_type = ($this->_data[$i]->function_type == 'giftcert') ? 'total' : 'per_user';
				}
				
			}

			if(!empty($ids)) {
				$ids = substr($ids,0,-1);
				$sql = 'SELECT coupon_id,count(user_id) as cnt FROM #__'.AWOCOUPON.'_user WHERE coupon_id IN ('.$ids.') GROUP BY coupon_id';
				$this->_db->setQuery( $sql );
				foreach($this->_db->loadObjectList() as $tmp) $ptr[$tmp->coupon_id]['usercount'] = $tmp->cnt;

				$sql = 'SELECT coupon_id,count(product_id) as cnt FROM #__'.AWOCOUPON.'_product WHERE coupon_id IN ('.$ids.') GROUP BY coupon_id';
				$this->_db->setQuery( $sql );
				foreach($this->_db->loadObjectList() as $tmp) @$ptr[$tmp->coupon_id]['assetcount'] = $tmp->cnt;

				$sql = 'SELECT coupon_id,count(category_id) as cnt FROM #__'.AWOCOUPON.'_category WHERE coupon_id IN ('.$ids.') GROUP BY coupon_id';
				$this->_db->setQuery( $sql );
				foreach($this->_db->loadObjectList() as $tmp) @$ptr[$tmp->coupon_id]['assetcount'] = $tmp->cnt;
				
			}
		}
			
		return $this->_data;
	}



	function _buildQuery() {
		// Get the WHERE, and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();

		$sql = 'SELECT c.id,c.coupon_code,c.num_of_uses,c.coupon_value_type,c.coupon_value,c.function_type2,
					 c.min_value,c.discount_type,c.function_type,c.startdate,c.expiration,c.published,0 as usercount,0 as productcount
				 FROM #__'.AWOCOUPON.' c
					'. $where.'
				GROUP BY c.id '
					. $orderby;
		return $sql;
	}

	/**
	 * Method to build the orderby clause of the query
	 **/
	function _buildContentOrderBy() {
		$filter_order		= JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.coupons.filter_order', 	'filter_order', 	'c.coupon_code', 'cmd' );
		$filter_order_Dir	= JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.coupons.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	/**
	 * Method to build the where clause of the query
	 **/
	function _buildContentWhere() {

		$filter_state 		= JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'coupons.filter_state', 		'filter_state', '', 'cmd' );
		$filter_coupon_value_type = JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.coupons.filter_coupon_value_type', 		'filter_coupon_value_type', 		'', 'cmd' );
		$filter_discount_type = JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.coupons.filter_discount_type', 		'filter_discount_type', 		'', 'cmd' );
		$filter_function_type = JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'.coupons.filter_function_type', 		'filter_function_type', 		'', 'cmd' );
		$search 			= JFactory::getApplication()->getUserStateFromRequest( AWOCOUPON_OPTION.'coupons.search', 			'search', 		'', 'string' );
		$search 			= awolibrary::dbescape( trim(JString::strtolower( $search ) ) );
	
		$where = array();
		

		if ( $filter_state ) {
			if($filter_state==1) {
				$current_date = awolibrary::getDate(null,'Y-m-d H:i:s','utc2utc');
				$where[] = 'c.published=1 
				   AND ( ((c.startdate IS NULL OR c.startdate="") 	AND (c.expiration IS NULL OR c.expiration="")) OR
						 ((c.expiration IS NULL OR c.expiration="") AND c.startdate<="'.$current_date.'") OR
						 ((c.startdate IS NULL OR c.startdate="") 	AND c.expiration>="'.$current_date.'") OR
						 (c.startdate<="'.$current_date.'"			AND c.expiration>="'.$current_date.'")
					   )
				'; 
			}
			elseif($filter_state==-1) {
				$current_date = awolibrary::getDate(null,'Y-m-d H:i:s','utc2utc');
				$where[] = '(c.published=-1 OR c.startdate>"'.$current_date.'" OR c.expiration<"'.$current_date.'")';
			}
			else $where[] = 'c.published='.(int)$filter_state;

		}
		if ( $filter_coupon_value_type ) $where[] = 'c.coupon_value_type = \''.$filter_coupon_value_type.'\'';
		if ( $filter_discount_type ) $where[] = 'c.discount_type = \''.$filter_discount_type.'\'';
		if ( $filter_function_type ) $where[] = 'c.function_type = \''.$filter_function_type.'\'';
		if ($search) $where[] = ' LOWER(c.coupon_code) LIKE '.$this->_db->Quote( '%'.awolibrary::dbescape( $search, true ).'%', false );

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}
	

	
	function delete($cids) {		
		
		$cids = implode( ',', $cids );

		$query = 'DELETE FROM #__'.AWOCOUPON.'_product WHERE coupon_id IN ('. $cids .')';
		$this->_db->setQuery( $query );
		if(!$this->_db->query()) {
			JFactory::getApplication()->enqueueMessage($this->_db->getErrorMsg(), 'error');
			return false;
		}

		$query = 'DELETE FROM #__'.AWOCOUPON.'_category WHERE coupon_id IN ('. $cids .')';
		$this->_db->setQuery( $query );
		if(!$this->_db->query()) {
			JFactory::getApplication()->enqueueMessage($this->_db->getErrorMsg(), 'error');
			return false;
		}

		$query = 'DELETE FROM #__'.AWOCOUPON.'_user WHERE coupon_id IN ('. $cids .')';
		$this->_db->setQuery($query);
		if(!$this->_db->query()) {
			JFactory::getApplication()->enqueueMessage($this->_db->getErrorMsg(), 'error');
			return false;
		}

		$query = 'DELETE FROM #__'.AWOCOUPON.'_history WHERE coupon_id IN ('. $cids .')';
		$this->_db->setQuery($query);
		if(!$this->_db->query()) {
			JFactory::getApplication()->enqueueMessage($this->_db->getErrorMsg(), 'error');
			return false;
		}

		$query = 'DELETE FROM #__'.AWOCOUPON.' WHERE id IN ('. $cids .')';
		$this->_db->setQuery($query);
		if(!$this->_db->query()) {
			JFactory::getApplication()->enqueueMessage($this->_db->getErrorMsg(), 'error');
			return false;
		}

		return $this->_db->getAffectedRows().' '.JText::_('COM_AWOCOUPON_MSG_ITEMS_DELETED');
	}
	
	
}

