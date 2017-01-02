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

class AwoCouponModelUsers extends JModel {
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
		//$this->setId((int)$array[0]);
		$id		= $mainframe->getUserStateFromRequest( $option.'.users.id', 	'id', 	JRequest::getVar( 'id' ), 'cmd' );
		$this->setId($id);

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
			
			$controller = new AwoCouponController( );
			$model_coupon = $controller->getModel('coupon');
			$model_coupon->setId($this->_id);
			$rows      	= & $model_coupon->getEntry();
			$rows->users = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			$this->_data = $rows;

		}
			
		return $this->_data;
	}

	/**
	 * Method to get the total
	 **/
	function getTotal() {
		// Lets load the files if it doesn't already exist
		if (empty($this->_total)) {
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
		if(!defined('VM_TABLEPREFIX')) require_once JPATH_ADMINISTRATOR.'/components/com_virtuemart/virtuemart.cfg.php';

		// Get the WHERE, and ORDER BY clauses for the query
		$orderby	= $this->_buildContentOrderBy();

		$sql = 'SELECT c.id,c.coupon_code,c.num_of_uses,c.coupon_value_type,c.coupon_value,
					 c.min_value,c.discount_type,c.function_type,c.expiration,u.user_id,
					 if(uv.user_id is NULL,us.name,uv.first_name) as first_name,uv.last_name
				 FROM #__awocoupon c
				 JOIN #__awocoupon_user u ON u.coupon_id=c.id
				 JOIN #__users us ON us.id=u.user_id
				 LEFT JOIN #__'.VM_TABLEPREFIX.'_user_info uv ON uv.user_id=u.user_id
				WHERE c.id='.$this->_id.'
				GROUP BY u.user_id
				'.$orderby
			; 
		return $sql;
	}

	/**
	 * Method to build the orderby clause of the query
	 **/
	function _buildContentOrderBy() {
		global $mainframe, $option;

		$filter_order		= $mainframe->getUserStateFromRequest( $option.'.users.filter_order', 	'filter_order', 	'c.coupon_code', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.users.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	
	function deleteusers($cids) {	
		$total = count($cids);
		$cids = implode( '\',\'', $cids );

		$sql = 'DELETE FROM #__awocoupon_user'
				. ' WHERE coupon_id='.(int)$this->_id.' AND user_id IN (\''. $cids .'\')';

		$this->_db->setQuery( $sql );
		if(!$this->_db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return $total.' '.JText::_('USERS DELETED');
	}
	

}
?>