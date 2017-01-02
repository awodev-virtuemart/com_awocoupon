<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');


if(version_compare( JVERSION, '3.0.0', 'ge' )) {
	class AwoCouponModelConnector extends JModelList { }
}

else {

	jimport('joomla.application.component.model');

	class AwoCouponModelConnector extends JModel { }
}


class AwoCouponModel extends AwoCouponModelConnector {
	var $_entry	 		= null;
	var $_id 			= null;
	var $_type			= null;


	function __construct() {
		parent::__construct();

		if(empty($this->_type)) $this->_type='general';

		$limit		= JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.'.$this->_type.'.limit', 'limit', JFactory::getApplication()->getCfg('list_limit'), 'int');
		$limitstart = JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.'.$this->_type.'.limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		$this->setId($cid[0]);

	}


	
	/**
	 * Overridden get method to get properties from the entry
	 **/
	function get($property, $default=null) {
		if ($this->_loadEntry()) {
			if(isset($this->_entry->$property)) {
				return $this->_entry->$property;
			}
		}
		return $default;
	}
	function &getEntry() {
	
		$row 		= JTable::getInstance($this->_type, 'AwoCouponTable');
		$row->load($this->_id);	
		$this->_entry = $row;

		return $this->_entry;
	}
	function setId($id) {
		// Set entry id and wipe data
		$this->_id	    = $id;
		$this->_entry	= null;
	}
	function getData() {
		if (empty($this->_data)) {
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_data;
	}
	function getTotal() {
		if (empty($this->_total)) {
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}
		return $this->_total;
	}
	function getPagination() {
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}
	
	function delete($cids) {		
		
		$row 		= JTable::getInstance($this->_type, 'AwoCouponTable');
		$tablename = $row->getTableName();
		$keyname = $row->getKeyName();
		foreach($cids as $k=>$i) $cids[$k] = (int)$i;

		$query = 'DELETE FROM '.$this->_db->{version_compare( JVERSION, '1.6.0', 'ge' ) ? 'quoteName' : 'nameQuote'}($tablename).' WHERE '.$keyname.' IN ('. implode( ',', $cids ) .')';
		$this->_db->setQuery( $query );
		if(!$this->_db->query()) {
			JFactory::getApplication()->enqueueMessage($this->_db->getErrorMsg(), 'error');
			return false;
		}

		return true;
	}
	function publish($cids = array(), $publish = 1) {
		$row 		= JTable::getInstance($this->_type, 'AwoCouponTable');
		$tablename = $row->getTableName();
		$keyname = $row->getKeyName();
		foreach($cids as $k=>$i) $cids[$k] = (int)$i;

		if (count( $cids )) {
			$query = 'UPDATE '.$this->_db->{version_compare( JVERSION, '1.6.0', 'ge' ) ? 'quoteName' : 'nameQuote'}($tablename).' SET published = '.(int)$publish.' WHERE '.$keyname.' IN ('. implode( ',', $cids ) .')';
			$this->_db->setQuery( $query );
		
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return true;
	}	
	function move($direction, $id,$filter=null) {
		$table = $this->getTable($this->_type);
		if (!$table->load($id)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		$where = '';
		if ($filter) $where = ' '.$filter.' = "'.$table->$filter.'" '; //.' AND published >= 0 ';
		//exit($where);
		if (!$table->move( $direction, $where )) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}	
	function saveorder($cid = array(), $order, $filter = null) {
		$table = $this->getTable($this->_type);
		$groupings = array();

		// update ordering values
		for( $i=0; $i < count($cid); $i++ )
		{
			$table->load( (int) $cid[$i] );
			// track categories
			if ($filter) $groupings[] = $table->$filter;

			if ($table->ordering != $order[$i])
			{
				$table->ordering = $order[$i];
				if (!$table->store()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}

		// execute updateOrder for each parent group
		if ($filter) {
			$groupings = array_unique( $groupings );
			foreach ($groupings as $group){
				$table->reorder(	$filter.' = '.(int) $group);
			}
		}

		return true;
	}

}
