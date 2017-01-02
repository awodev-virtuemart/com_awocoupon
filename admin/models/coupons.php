<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class AwoCouponModelCoupons extends JModel
{
	var $_pagination = null;
	var $_id = null;

	/**
	 * Constructor
	 **/
	function __construct() {
		parent::__construct();

		global $mainframe, $option;

		$limit		= $mainframe->getUserStateFromRequest( $option.'.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);

	}

	/**
	 * Method to set the identifier
	 **/
	function setId($id) {
		// Set id and wipe data
		$this->_id	 = $id;
		$this->_data = null;
	}

	/**
	 * Method to get data
	 **/
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
				$ptr[$row->id]['productcount'] = &$this->_data[$i]->productcount;
				
				$this->_data[$i]->num_of_uses_type = '';
				if(!empty($this->_data[$i]->num_of_uses)) {
					$this->_data[$i]->num_of_uses_type = ($this->_data[$i]->function_type == 'giftcert') ? 'total' : 'per_user';
				}
				
			}

			if(!empty($ids)) {
				$ids = substr($ids,0,-1);
				$sql = 'SELECT coupon_id,count(user_id) as cnt FROM #__awocoupon_user WHERE coupon_id IN ('.$ids.') GROUP BY coupon_id';
				$this->_db->setQuery( $sql );
				foreach($this->_db->loadObjectList() as $tmp) $ptr[$tmp->coupon_id]['usercount'] = $tmp->cnt;

				$sql = 'SELECT coupon_id,count(product_id) as cnt FROM #__awocoupon_product WHERE coupon_id IN ('.$ids.') GROUP BY coupon_id';
				$this->_db->setQuery( $sql );
				foreach($this->_db->loadObjectList() as $tmp) $ptr[$tmp->coupon_id]['productcount'] = $tmp->cnt;
			}
		}
			
		return $this->_data;
	}

	/**
	 * Method to get the total
	 **/
	function getTotal() {
		// Lets load the files if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}
	
	/**
	 * Method to get a pagination object
	 **/
	function getPagination() {
		// Lets load the files if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query
	 **/
	function _buildQuery() {
		// Get the WHERE, and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();

		$sql = 'SELECT c.id,c.coupon_code,c.num_of_uses,c.coupon_value_type,c.coupon_value,
					 c.min_value,c.discount_type,c.function_type,c.expiration,c.published,0 as usercount,0 as productcount
				 FROM #__awocoupon c
					'. $where.'
				GROUP BY c.id '
					. $orderby;

		return $sql;
	}

	/**
	 * Method to build the orderby clause of the query
	 **/
	function _buildContentOrderBy() {
		global $mainframe, $option;

		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.coupons.filter_order', 	'filter_order', 	'c.coupon_code', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.coupons.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	/**
	 * Method to build the where clause of the query
	 **/
	function _buildContentWhere() {
		global $mainframe, $option;

		$filter_state 		= $mainframe->getUserStateFromRequest( $option.'coupons.filter_state', 		'filter_state', '', 'cmd' );
		$filter_coupon_value_type = $mainframe->getUserStateFromRequest( $option.'.coupons.filter_coupon_value_type', 		'filter_coupon_value_type', 		'', 'cmd' );
		$filter_discount_type = $mainframe->getUserStateFromRequest( $option.'.coupons.filter_discount_type', 		'filter_discount_type', 		'', 'cmd' );
		$filter_function_type = $mainframe->getUserStateFromRequest( $option.'.coupons.filter_function_type', 		'filter_function_type', 		'', 'cmd' );
		$search 			= $mainframe->getUserStateFromRequest( $option.'coupons.search', 			'search', 		'', 'string' );
		$search 			= $this->_db->getEscaped( trim(JString::strtolower( $search ) ) );
	
		$where = array();
		

		if ( $filter_state ) {
			if($filter_state==1) $where[] = 'c.published=1 AND (c.expiration IS NULL OR c.expiration="" OR c.expiration>="'.date('Y-m-d').'")'; 
			elseif($filter_state==-1) $where[] = '(c.published=-1 OR c.expiration<"'.date('Y-m-d').'")';
		}
		if ( $filter_coupon_value_type ) $where[] = 'c.coupon_value_type = \''.$filter_coupon_value_type.'\'';
		if ( $filter_discount_type ) $where[] = 'c.discount_type = \''.$filter_discount_type.'\'';
		if ( $filter_function_type ) $where[] = 'c.function_type = \''.$filter_function_type.'\'';
		if ($search) $where[] = ' LOWER(c.coupon_code) LIKE '.$this->_db->Quote( '%'.$this->_db->getEscaped( $search, true ).'%', false );

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}
	
	/**
	 * Method to (un)publish
	 **/
	function publish($cid = array(), $publish = 1) {
		$user 	=& JFactory::getUser();

		if (count( $cid )) {
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__awocoupon SET published = '.(int)$publish.' WHERE id IN ('. $cids .')';
			$this->_db->setQuery( $query );
		
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return $cid;
	}
	
	/**
	 * Method to remove
	 **/
	function delete($cids) {		
		global $mainframe;
		
		$cids = implode( ',', $cids );

		$query = 'DELETE FROM #__awocoupon_product WHERE coupon_id IN ('. $cids .')';
		$this->_db->setQuery( $query );
		if(!$this->_db->query()) {
			$mainframe->enqueueMessage($this->_db->getErrorMsg(), 'error');
			return false;
		}

		$query = 'DELETE FROM #__awocoupon_user WHERE coupon_id IN ('. $cids .')';
		$this->_db->setQuery($query);
		if(!$this->_db->query()) {
			$mainframe->enqueueMessage($this->_db->getErrorMsg(), 'error');
			return false;
		}

		$query = 'DELETE FROM #__awocoupon_user_uses WHERE coupon_id IN ('. $cids .')';
		$this->_db->setQuery($query);
		if(!$this->_db->query()) {
			$mainframe->enqueueMessage($this->_db->getErrorMsg(), 'error');
			return false;
		}

		$query = 'DELETE FROM #__awocoupon WHERE id IN ('. $cids .')';
		$this->_db->setQuery($query);
		if(!$this->_db->query()) {
			$mainframe->enqueueMessage($this->_db->getErrorMsg(), 'error');
			return false;
		}

		$total 	= count( $cids );
		$msg 	= $total.' '.JText::_('COUPONS DELETED');
		return $msg;
	}
	
}
?>