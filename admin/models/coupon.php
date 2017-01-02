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

class AwoCouponModelCoupon extends JModel
{

	var $_entry	 		= null;
	var $_id 			= null;
	var $_sections 		= null;

	/**
	 * Constructor
	 **/
	function __construct()
	{
		parent::__construct();

		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		$this->setId($cid[0]);
	}

	/**
	 * Method to set the identifier
	 **/
	function setId($id)
	{
		// Set entry id and wipe data
		$this->_id	    = $id;
		$this->_entry	= null;
	}
	
	/**
	 * Overridden get method to get properties from the entry
	 **/
	function get($property, $default=null)
	{
		if ($this->_loadEntry()) {
			if(isset($this->_entry->$property)) {
				return $this->_entry->$property;
			}
		}
		return $default;
	}

	/**
	 * Method to get entry data
	 **/
	function &getEntry() {
	
		if ($this->_loadEntry()) {
			$this->_entry->userlist = $this->_entry->productlist = array();	
			$this->_entry->num_of_uses_type = '';
			if(!empty($this->_entry->num_of_uses)) {
				$this->_entry->num_of_uses_type = ($this->_entry->function_type == 'giftcert') ? 'total' : 'per_user';
			}
			else $this->_entry->num_of_uses = '';

			$sql = 'SELECT user_id FROM #__awocoupon_user WHERE coupon_id='.$this->_id;
			$this->_db->setQuery($sql);
			$tmp = $this->_db->loadObjectList();
			foreach($tmp as $tmp2) $this->_entry->userlist[$tmp2->user_id] = $tmp2->user_id;

			$sql = 'SELECT product_id FROM #__awocoupon_product WHERE coupon_id='.$this->_id;
			$this->_db->setQuery($sql);
			$tmp = $this->_db->loadObjectList();
			foreach($tmp as $tmp2) $this->_entry->productlist[$tmp2->product_id] = $tmp2->product_id;
		}
		else  $this->_initEntry();

		return $this->_entry;
	}

	/**
	 * Method to get hits
	 **/
	function getUserList() {
		if(!defined('VM_TABLEPREFIX')) require_once JPATH_ADMINISTRATOR.'/components/com_virtuemart/virtuemart.cfg.php';
		$query = 'SELECT u.id AS user_id,IF(v.user_id IS NULL,u.name,v.first_name) as first_name,v.last_name 
					FROM #__users u
					LEFT JOIN #__'.VM_TABLEPREFIX.'_user_info v ON v.user_id=u.id
				   GROUP BY u.id 
				   ORDER BY v.last_name, v.first_name, u.name, u.id';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
		
	}

	function getProductList() {
		if(!defined('VM_TABLEPREFIX')) require_once JPATH_ADMINISTRATOR.'/components/com_virtuemart/virtuemart.cfg.php';
		$query = 'SELECT product_id,product_name,product_sku FROM #__'.VM_TABLEPREFIX.'_product ORDER BY product_name,product_id';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	/**
	 * Method to load entry data
	 **/
	function _loadEntry()
	{
		// Lets load the entry if it doesn't already exist
		if (empty($this->_entry))
		{
			$query = 'SELECT * FROM #__awocoupon WHERE id = '.$this->_id;
			$this->_db->setQuery($query);
			$this->_entry = $this->_db->loadObject();
			
			
			return (boolean) $this->_entry;
		}
		return true;
	}

	/**
	 * Method to initialise the entry data
	 **/
	function _initEntry()
	{
		// Lets load the entry if it doesn't already exist
		if (empty($this->_entry))
		{
			
			$entry = new stdClass();

			$this->_entry					= & JTable::getInstance('coupons', 'Table');
			$this->_entry->num_of_uses_type = '';
			return (boolean) $this->_entry;
		}
		return true;
	}

	/**
	 * Method to store the entry
	 **/
	function store($data) {
		global $mainframe;
		
		if(empty($data['expiration'])) $data['expiration'] = null;

		$errors = '';
		if(!empty($data['num_of_uses_type']) && $data['num_of_uses_type']!='total' && $data['num_of_uses_type']!='per_user') $errors .= '<br>'.JText::_('PLEASE ENTER A VALID NUMBER OF USES TYPE');
		if(!empty($data['num_of_uses']) && !ctype_digit($data['num_of_uses'])) $errors .= '<br>'.JText::_('PLEASE ENTER A VALID NUMBER OF USES');
		if( (!empty($data['num_of_uses_type']) && empty($data['num_of_uses'])) || (empty($data['num_of_uses_type']) && !empty($data['num_of_uses']))) $errors .= '<br>'.JText::_('PLEASE ENTER A VALID NUMBER OF USES');
		if (!empty($errors)) {
			$mainframe->enqueueMessage($errors, 'error');
			return false;
		}
		
		$data['function_type'] = $data['num_of_uses_type']=='total' ? 'giftcert' : 'coupon';

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
				
		$row 		= & JTable::getInstance('coupons', 'Table');
		$user		=& JFactory::getUser();
		$details	= JRequest::getVar( 'details', array(), 'post', 'array');
		$nullDate	= $this->_db->getNullDate();
		
		// bind it to the table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}


		// sanitise fields
		$row->id 			= (int) $row->id;
		
		

		// Make sure the data is valid
		if (!$row->check()) {
			$mainframe->enqueueMessage($this->_db->stderr(), 'error');
			//JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}

		if(empty($row->id)) {
		//Error: That coupon code already exists. Please try again.
			$sql = 'SELECT id FROM #__awocoupon WHERE coupon_code = \''.$row->coupon_code.'\'';
			$this->_db->setQuery($sql);
			$tmp = $this->_db->loadObjectList();
			//printrx($tmp);
			if(!empty($tmp)) {
				$mainframe->enqueueMessage(JText::_('ERROR DUPLICATE COUPON CODE'), 'error');
				return false;
			}
		}
				
		// clean out the products/users tables
		if(!empty($row->id)) {
			$query = 'DELETE FROM #__awocoupon_user WHERE coupon_id = '.$row->id;
			$this->_db->setQuery($query);
			$this->_db->query();

			$query = 'DELETE FROM #__awocoupon_product WHERE coupon_id = '.$row->id;
			$this->_db->setQuery($query);
			$this->_db->query();
		}
		
		//correct invalid data
		//if($row->coupon_value_type == 'total') $row->discount_type = 'overall';
		if(empty($row->num_of_uses)) $row->function_type = 'coupon';
		
		// Store the entry to the database
		if (!$row->store(true)) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}

		//store products and users if chosen
		if(!empty($data['userlist'])) {
			$insert_str = '';
			foreach($data['userlist'] as $tmp) $insert_str .= '('.$row->id.',\''.$tmp.'\'),';
			if(!empty($insert_str)) {
				$query = 'INSERT INTO #__awocoupon_user (coupon_id, user_id) VALUES '.substr($insert_str,0,-1);
				$this->_db->setQuery($query);
				$this->_db->query();
			}
		}
		
		if(!empty($data['productlist'])) {
			$insert_str = '';
			foreach($data['productlist'] as $tmp) $insert_str .= '('.$row->id.',\''.$tmp.'\'),';
			if(!empty($insert_str)) {
				$query = 'INSERT INTO #__awocoupon_product (coupon_id, product_id) VALUES '.substr($insert_str,0,-1);
				$this->_db->setQuery($query);
				$this->_db->query();
			}
		}
				
		$this->_entry	=& $row;
		
		return true;
	}

	
}
?>