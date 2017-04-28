<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponModelCoupon extends AwoCouponModel {

	public function __construct() {
		$this->_type = 'coupons';
		parent::__construct();

	}
	

	function &getEntry() {
	
		if ($this->_loadEntry()) {
			$this->_entry->userlist = $this->_entry->assetlist = array();	
			$this->_entry->num_of_uses_type = '';
			$this->_entry->asset1_function_type = $this->_entry->function_type2;
			if(!empty($this->_entry->num_of_uses)) {
				$this->_entry->num_of_uses_type = ($this->_entry->function_type == 'giftcert') ? 'total' : 'per_user';
			}
			else $this->_entry->num_of_uses = '';

			$sql = 'SELECT user_id FROM #__'.AWOCOUPON.'_user WHERE coupon_id='.$this->_id;
			$this->_db->setQuery($sql);
			$tmp = $this->_db->loadObjectList();
			foreach($tmp as $tmp2) $this->_entry->userlist[$tmp2->user_id] = $tmp2->user_id;

			
			if(!defined('VMLANG')) {
				if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php');
				VmConfig::loadConfig();
			}
			
			if($this->_entry->function_type2=='product') {
				$sql = 'SELECT a.coupon_id,a.product_id AS asset_id,c.product_name AS asset_name
						  FROM #__'.AWOCOUPON.'_product a
						  JOIN #__virtuemart_products b ON b.virtuemart_product_id=a.product_id
						  JOIN #__virtuemart_products_'.VMLANG.' c USING (virtuemart_product_id)
						 WHERE a.coupon_id IN ('.(int)$this->_id.')';
				$this->_db->setQuery($sql);
				$this->_entry->assetlist = $this->_db->loadObjectList();
			}
			elseif($this->_entry->function_type2=='category') {
				$sql = 'SELECT a.coupon_id,a.category_id AS asset_id,c.category_name AS asset_name
						  FROM #__'.AWOCOUPON.'_category a
						  JOIN #__virtuemart_categories b ON b.virtuemart_category_id=a.category_id
						  JOIN #__virtuemart_categories_'.VMLANG.' c USING (virtuemart_category_id)
						 WHERE a.coupon_id IN ('.(int)$this->_id.')';
				$this->_db->setQuery($sql);
				$this->_entry->assetlist = $this->_db->loadObjectList();
			}
			
			
			$this->_db->setQuery('SELECT a.user_id,u.name as user_name FROM #__'.AWOCOUPON.'_user a JOIN #__users u ON u.id=a.user_id WHERE a.coupon_id='.$this->_id);
			$this->_entry->userlist = $this->_db->loadObjectList();

			

		}
		else  $this->_initEntry();

		return $this->_entry;
	}



	function _loadEntry() {
		// Lets load the entry if it doesn't already exist
		if (empty($this->_entry)) {
			$query = 'SELECT c.* FROM #__'.AWOCOUPON.' c  WHERE c.id = '.$this->_id;
			$this->_db->setQuery($query);
			$this->_entry = $this->_db->loadObject();
			
			
			return (boolean) $this->_entry;
		}
		return true;
	}

	/**
	 * Method to initialise the entry data
	 **/
	function _initEntry() {
		// Lets load the entry if it doesn't already exist
		if (empty($this->_entry))
		{
			
			$entry = new stdClass();

			$this->_entry					= JTable::getInstance('coupons', 'AwoCouponTable');
			$this->_entry->num_of_uses_type = '';
			$this->_entry->userlist = $this->_entry->assetlist = array();		
			$this->_entry->asset1_function_type = null;
			return (boolean) $this->_entry;
		}
		return true;
	}

	function store($data) {

		if(empty($data['startdate'])) $data['startdate'] = null;
		if(empty($data['expiration'])) $data['expiration'] = null;

		$errors = '';
		if(!empty($data['num_of_uses_type']) && $data['num_of_uses_type']!='total' && $data['num_of_uses_type']!='per_user') $errors .= '<br>'.JText::_('COM_AWOCOUPON_CP_NUMBER_USES_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
		if(!empty($data['num_of_uses']) && !ctype_digit($data['num_of_uses'])) $errors .= '<br>'.JText::_('COM_AWOCOUPON_CP_NUMBER_USES').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
		if( (!empty($data['num_of_uses_type']) && empty($data['num_of_uses'])) || (empty($data['num_of_uses_type']) && !empty($data['num_of_uses']))) $errors .= '<br>'.JText::_('COM_AWOCOUPON_CP_NUMBER_USES').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
		if (!empty($errors)) {
			JFactory::getApplication()->enqueueMessage($errors, 'error');
			return false;
		}
		
		$data['function_type'] = $data['num_of_uses_type']=='total' ? 'giftcert' : 'coupon';

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
				
		$row 		= JTable::getInstance('coupons', 'AwoCouponTable');
		$user		= JFactory::getUser();
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
			JFactory::getApplication()->enqueueMessage($this->_db->stderr(), 'error');
			//JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}

		if(empty($row->id)) {
		//Error: That coupon code already exists. Please try again.
			$sql = 'SELECT id FROM #__'.AWOCOUPON.' WHERE coupon_code = \''.$row->coupon_code.'\'';
			$this->_db->setQuery($sql);
			$tmp = $this->_db->loadObjectList();
			//printrx($tmp);
			if(!empty($tmp)) {
				JFactory::getApplication()->enqueueMessage(JText::_('COM_AWOCOUPON_ERR_DUPLICATE_CODE'), 'error');
				return false;
			}
		}
		
		
		if($row->function_type=='coupon' && empty($data['assetlist']) && $row->discount_type=='specific') {
			$errors[] = JText::_('COM_AWOCOUPON_CP_ERR_ONE_SPECIFIC');
		}
		
		//correct invalid data
		//if($row->coupon_value_type == 'total') $row->discount_type = 'overall';
		if(empty($row->num_of_uses)) $row->function_type = 'coupon';
		$row->function_type2 = empty($data['asset1_function_type']) ? null : $data['asset1_function_type'];
		
		// Store the entry to the database
		if (!$row->store(true)) {
			JError::raiseError( 500, $this->_db->stderr() );
			return false;
		}



		
		// clean out the products/users tables
		if(!empty($row->id)) {
			$this->_db->setQuery('DELETE FROM #__'.AWOCOUPON.'_user WHERE coupon_id = '.$row->id); $this->_db->query();
			$this->_db->setQuery('DELETE FROM #__'.AWOCOUPON.'_product WHERE coupon_id = '.$row->id); $this->_db->query();
			$this->_db->setQuery('DELETE FROM #__'.AWOCOUPON.'_category WHERE coupon_id = '.$row->id); $this->_db->query();
		}
		
		//store products and users if chosen
		if(!empty($data['userlist'])) {
			$insert_str = '';
			foreach($data['userlist'] as $tmp) $insert_str .= '('.$row->id.',\''.$tmp.'\'),';
			if(!empty($insert_str)) {
				$query = 'INSERT INTO #__'.AWOCOUPON.'_user (coupon_id, user_id) VALUES '.substr($insert_str,0,-1);
				$this->_db->setQuery($query);
				$this->_db->query();
			}
		}
		
		if(!empty($data['assetlist'])) {
			$insert_str = '';
			foreach($data['assetlist'] as $tmp) $insert_str .= '('.$row->id.',\''.$tmp.'\'),';
			if(!empty($insert_str)) {
				if($row->function_type2=='product') {
					$this->_db->setQuery('INSERT INTO #__'.AWOCOUPON.'_product (coupon_id, product_id) VALUES '.substr($insert_str,0,-1));
					$this->_db->query();
				}
				elseif($row->function_type2=='category') {
					$this->_db->setQuery('INSERT INTO #__'.AWOCOUPON.'_category (coupon_id, category_id) VALUES '.substr($insert_str,0,-1));
					$this->_db->query();
				}
			}
		}
				
		$this->_entry	= $row;
		
		return true;
	}
}
