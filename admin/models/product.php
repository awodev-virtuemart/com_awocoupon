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

class AwoCouponModelProduct extends JModel
{

	var $_entry	 		= null;
	var $_id 			= null;
	var $_sections 		= null;

	/**
	 * Constructor
	 **/
	function __construct() {
		parent::__construct();

		global $mainframe,$option;
		$id		= $mainframe->getUserStateFromRequest( $option.'.products.id', 	'id', 	JRequest::getVar( 'id' ), 'cmd' );
		$this->setId($id);
	}

	/**
	 * Method to set the identifier
	 **/
	function setId($id) {
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
		}

		return $this->_entry;
	}

	/**
	 * Method to get hits
	 **/
	function getProductList() {
		if(!defined('VM_TABLEPREFIX')) require_once JPATH_ADMINISTRATOR.'/components/com_virtuemart/virtuemart.cfg.php';
		$sql = 'SELECT v.product_id,v.product_name,v.product_sku
				 FROM #__'.VM_TABLEPREFIX.'_product v
				 LEFT JOIN #__awocoupon_product c ON c.product_id=v.product_id AND c.coupon_id='.$this->_id.'
				WHERE c.product_id IS NULL
				ORDER BY v.product_name, v.product_id';
		$this->_db->setQuery($sql);
		return  $this->_db->loadObjectList();
		
	}

	
	/**
	 * Method to load entry data
	 **/
	function _loadEntry() {
		if (empty($this->_entry)) {
			$controller = new AwoCouponController( );
			$model_coupon = $controller->getModel('coupon');
			$model_coupon->setId($this->_id);
			$this->_entry      	= & $model_coupon->getEntry();

			return (boolean) $this->_entry;
		}
		return true;
	}


	/**
	 * Method to store the entry
	 **/
	function store($data) {
		global $mainframe;

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		//store products and users if chosen
		
		if(!empty($data['productlist'])) {
			$insert_str = '';
			foreach($data['productlist'] as $product_id) $insert_str .= '('.$this->_id.',\''.$product_id.'\'),';
		
			if(!empty($insert_str)) {
				$query = 'INSERT INTO #__awocoupon_product (coupon_id, product_id) VALUES '.substr($insert_str,0,-1);
				$this->_db->setQuery($query);
				$this->_db->query();
			}
		} else {
			$mainframe->enqueueMessage(JText::_('SELECT A PRODUCT'), 'error');
			return false;
		}
		
		return true;
	}

	
}
?>