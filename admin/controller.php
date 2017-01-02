<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');


if(version_compare( JVERSION, '3.0.0', 'ge' )) { class AwoCouponControllerConnect extends JControllerForm {} }
else {
	jimport('joomla.application.component.controller');
	class AwoCouponControllerConnect extends JController {}
}


class AwoCouponController extends AwoCouponControllerConnect {
	/**
	 * Constructor
	 **/
	function __construct() {
		parent::__construct();

		// Register Extra task
		
		$this->registerTask( 'addcoupon',		'editcoupon' );
		$this->registerTask( 'savecoupon', 		'savecoupon' );
		$this->registerTask( 'cancelcoupon',		'cancelcoupon' );
		$this->registerTask( 'editcoupon',		'editcoupon' );
		$this->registerTask( 'removecoupon',		'removecoupon' );
		$this->registerTask( 'publishcoupon', 	'publishcoupon' );
		$this->registerTask( 'unpublishcoupon', 	'unpublishcoupon' );

		
		$this->registerTask( 'processreport', 	'processreport' );
		
		$this->registerTask( 'saveimport', 		'saveimport' );
		$this->registerTask( 'cancelimport',		'cancelimport' );
		
		$this->registerTask( 'saveconfig',		'saveconfig' );
		$this->registerTask( 'applyconfig',		'saveconfig' );
		$this->registerTask( 'cancelconfig', 	'cancelconfig' );


		$this->registerTask( 'addgiftcertproduct',		'editgiftcertproduct' );
		$this->registerTask( 'savegiftcertproduct', 		'savegiftcertproduct' );
		$this->registerTask( 'cancelgiftcertproduct',		'cancelgiftcertproduct' );
		$this->registerTask( 'editgiftcertproduct',		'editgiftcertproduct' );
		$this->registerTask( 'removegiftcertproduct',		'removegiftcertproduct' );
		$this->registerTask( 'publishgiftcertproduct', 	'publishgiftcertproduct' );
		$this->registerTask( 'unpublishgiftcertproduct', 	'unpublishgiftcertproduct' );
	}
	function display($cachable = false,$urlparams=false)  {
		JRequest::setVar('view', JRequest::getCmd('view', 'dashboard'));		
		parent::display($cachable,$urlparams);
	}

	
	function savecoupon() {
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		//Sanitize
		$post = JRequest::get( 'post' );
		$model = $this->getModel('coupon');

		if ( $model->store($post) ) {

			$this->setRedirect('index.php?option='.AWOCOUPON_OPTION.'&view=coupons', JText::_( 'COM_AWOCOUPON_MSG_DATA_SAVED' ));
		} else {
			return $this->execute('editcoupon');
		}


	}
	function cancelcoupon() {	
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$this->setRedirect( 'index.php?option='.AWOCOUPON_OPTION.'&view=coupons' );
	}
	function editcoupon( ) {
	
		JRequest::setVar( 'view', 'coupon' );
		JRequest::setVar( 'hidemainmenu', 1 );
		$model 	= $this->getModel('coupon');
		parent::display();
	}
	function removecoupon() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel('coupons');

			if(!$model->delete($cid)) JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
			else {
				$this->setRedirect( 'index.php?option='.AWOCOUPON_OPTION.'&view=coupons', count( $cid ).' '.JText::_('COM_AWOCOUPON_MSG_ITEMS_DELETED') );
			}
		}
	}
	function publishcoupon() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel('coupons');

			if(!$model->publish($cid, 1)) JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
			else {
				$this->setRedirect( 'index.php?option='.AWOCOUPON_OPTION.'&view=coupons', JText::_( 'COM_AWOCOUPON_MSG_ITEMS_PUBLISHED') );
			}
		}
	}
	function unpublishcoupon() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel('coupons');

			if(!$model->publish($cid, -1)) JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
			else {
				$this->setRedirect( 'index.php?option='.AWOCOUPON_OPTION.'&view=coupons', JText::_( 'COM_AWOCOUPON_MSG_ITEMS_UNPUBLISHED') );
			}
		}
	}

	function publishplugin() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel('installation');

			if(!$model->publish($cid, 1)) JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
			else {
				$this->setRedirect( 'index.php?option='.AWOCOUPON_OPTION.'&view=installation', JText::_( 'COM_AWOCOUPON_MSG_ITEMS_PUBLISHED') );
			}
		}
	}
	function unpublishplugin() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel('installation');

			if(!$model->publish($cid, 0)) JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
			else {
				$this->setRedirect( 'index.php?option='.AWOCOUPON_OPTION.'&view=installation', JText::_( 'COM_AWOCOUPON_MSG_ITEMS_UNPUBLISHED') );
			}
		}
	}
	
	function ajax_elements() {
		$db = JFactory::getDBO();
		$q = JRequest::getVar( 'term' );
		if(empty($q) || strlen($q)<2) exit;

		if(!defined('VMLANG')) {
			if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php');
			VmConfig::loadConfig();
		}

		$type = JRequest::getVar( 'type' );
		$q = $db->Quote( awolibrary::dbescape( trim(JString::strtolower( $q ) ), true ).'%', false );
		
		$result = array();
		switch($type) {
			case 'product':
				$sql = 'SELECT p.virtuemart_product_id AS id,CONCAT(lang.product_name," (",p.product_sku,")") AS label 
						  FROM #__virtuemart_products p
						  JOIN `#__virtuemart_products_'.VMLANG.'` as lang using (`virtuemart_product_id`)
						 WHERE p.published=1 AND CONCAT(lang.product_name," (",p.product_sku,")") LIKE '.$q.' ORDER BY label,p.product_sku LIMIT 25';
				break;
			case 'category':
				$sql = 'SELECT c.virtuemart_category_id AS id,lang.category_name AS label
						  FROM #__virtuemart_categories c
						  JOIN `#__virtuemart_categories_'.VMLANG.'` as lang using (`virtuemart_category_id`)
						 WHERE c.published=1 AND lang.category_name LIKE '.$q.' ORDER BY lang.category_name,c.virtuemart_category_id LIMIT 25';
				break;
			case 'user':
				$sql = 'SELECT id,username as label FROM #__users WHERE username LIKE '.$q.' ORDER BY username LIMIT 25';
				break;
		}
		if(!empty($sql)) {
			$db->setQuery($sql);
			foreach($db->loadObjectList() as $row) array_push($result, array("id"=>$row->id, "label"=>$row->label, "value" => strip_tags($row->label)));
		}

		echo json_encode($result);
		exit;
	}
	function ajax_elements_all() {
		$db = JFactory::getDBO();

		if(!defined('VMLANG')) {
			if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR.'/components/com_virtuemart/helpers/config.php');
			VmConfig::loadConfig();
		}

		$type = JRequest::getVar( 'type' );
		
		$result = array();
		switch($type) {
			case 'product':
				$sql = 'SELECT p.virtuemart_product_id AS id,CONCAT(lang.product_name," (",p.product_sku,")") AS label 
						  FROM #__virtuemart_products p
						  JOIN `#__virtuemart_products_'.VMLANG.'` as lang using (`virtuemart_product_id`)
						 WHERE p.published=1 ORDER BY label,p.product_sku';
				break;
			case 'category':
				$sql = 'SELECT c.virtuemart_category_id AS id,lang.category_name AS label
						  FROM #__virtuemart_categories c
						  JOIN `#__virtuemart_categories_'.VMLANG.'` as lang using (`virtuemart_category_id`)
						 WHERE c.published=1 ORDER BY lang.category_name,c.virtuemart_category_id';
				break;
			case 'user':
				$sql = 'SELECT id,username as label FROM #__users ORDER BY username,id';
				break;
		}
		if(!empty($sql)) {
			$db->setQuery($sql);
			foreach($db->loadObjectList() as $row) array_push($result, array("id"=>$row->id, "label"=>$row->label, "value" => strip_tags($row->label)));
		}

		echo json_encode($result);
		exit;
	}
	
}