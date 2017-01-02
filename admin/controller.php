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

class AwoCouponController extends JController {
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

		$this->registerTask( 'adduser',			'adduser' );
		$this->registerTask( 'saveuser',		'saveuser' );
		$this->registerTask( 'canceluser', 		'canceluser' );
		$this->registerTask( 'removeuser', 		'removeuser' );
		
		$this->registerTask( 'addproduct',		'addproduct' );
		$this->registerTask( 'saveproduct',		'saveproduct' );
		$this->registerTask( 'cancelproduct', 	'cancelproduct' );
		$this->registerTask( 'removeproduct', 	'removeproduct' );
		
	}

	
	function savecoupon() {
		global $mainframe;
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		//Sanitize
		$post = JRequest::get( 'post' );
		$model = $this->getModel('coupon');

		if ( $model->store($post) ) {

			$cache = &JFactory::getCache('page');
			$cache->clean();

			$this->setRedirect('index.php?option=com_awocoupon&view=coupons', JText::_( 'COUPON SAVED' ));
		} else {
			$msg = JText::_( 'ERROR SAVING COUPON' );
			$mainframe->enqueueMessage(JText::_( 'ERROR SAVING COUPON' ), 'error');
			return $this->execute('editcoupon');
		}


	}
	function cancelcoupon() {
	
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$this->setRedirect( 'index.php?option=com_awocoupon&view=coupons' );
	}
	function editcoupon( ) {
	
		JRequest::setVar( 'view', 'coupon' );
		JRequest::setVar( 'hidemainmenu', 1 );
		$model 	= $this->getModel('coupon');
		parent::display();
	}
	function removecoupon() {
		global $mainframe;
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_( 'SELECT A COUPON' ), 'error');
		} else {

			$model = $this->getModel('coupons');

			if(!$model->delete($cid)) $mainframe->enqueueMessage($model->getError(), 'error');
			else {
				$cache = &JFactory::getCache('com_awocoupon');
				$cache->clean();
				$this->setRedirect( 'index.php?option=com_awocoupon&view=coupons', count( $cid ).' '.JText::_('COUPONS DELETED') );
			}
		}
	}
	function publishcoupon() {
		global $mainframe;
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_( 'SELECT A COUPON' ), 'error');
		} else {

			$model = $this->getModel('coupons');

			if(!$model->publish($cid, 1)) $mainframe->enqueueMessage($model->getError(), 'error');
			else {
				$cache = &JFactory::getCache('com_awocoupon');
				$cache->clean();
				$this->setRedirect( 'index.php?option=com_awocoupon&view=coupons', JText::_( 'COUPONS PUBLISHED') );
			}
		}
	}
	function unpublishcoupon() {
		global $mainframe;
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_( 'SELECT A COUPON' ), 'error');
		} else {

			$model = $this->getModel('coupons');

			if(!$model->publish($cid, -1)) $mainframe->enqueueMessage($model->getError(), 'error');
			else {
				$cache = &JFactory::getCache('com_awocoupon');
				$cache->clean();
				$this->setRedirect( 'index.php?option=com_awocoupon&view=coupons', JText::_( 'COUPONS UNPUBLISHED') );
			}
		}
	}

	
	function adduser() { $this->setRedirect('index.php?option=com_awocoupon&view=user'); }
	function canceluser() { $this->setRedirect('index.php?option=com_awocoupon&view=users'); }
	function saveuser() { 
		global $mainframe;
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		//Sanitize
		$post = JRequest::get( 'post' );
		$model = $this->getModel('user');

		if ( $model->store($post) ) {

			$cache = &JFactory::getCache('page');
			$cache->clean();

			$this->setRedirect('index.php?option=com_awocoupon&view=users', JText::_( 'USER SAVED' ));
		} else {
			$mainframe->enqueueMessage(JText::_( 'ERROR SAVING USER' ), 'error');
			return $this->execute('adduser');
		}

	}
	function removeuser() {
		global $mainframe, $option;
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid		= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'SELECT A USER' ) );
		}

		$model = $this->getModel('users');
		$coupon_id = $mainframe->getUserStateFromRequest( $option.'.users.id', 	'id', 	JRequest::getVar( 'id' ), 'cmd' );

		$msg = $model->deleteusers($cid);
		if(!$msg) {
			$msg = '';
			JError::raiseError(500, $model->getError());
		} else {
			$cache = &JFactory::getCache('com_awocoupon');
			$cache->clean();
		}

		//$mainframe->enqueueMessage($msg, 'message');
		$this->setRedirect( 'index.php?option=com_awocoupon&view=users&tmpl=component',$msg );
	}

	
	function addproduct() { $this->setRedirect('index.php?option=com_awocoupon&view=product'); }
	function cancelproduct() { $this->setRedirect('index.php?option=com_awocoupon&view=products'); }
	function saveproduct() { 
		global $mainframe;
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		//Sanitize
		$post = JRequest::get( 'post' );
		$model = $this->getModel('product');

		if ( $model->store($post) ) {

			$cache = &JFactory::getCache('page');
			$cache->clean();

			$this->setRedirect('index.php?option=com_awocoupon&view=products', JText::_( 'PRODUCT SAVED' ));
		} else {
			$mainframe->enqueueMessage(JText::_( 'ERROR SAVING PRODUCT' ), 'error');
			return $this->execute('addproduct');
		}

	}
	function removeproduct() {
		global $mainframe, $option;
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid		= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'SELECT A PRODUCT' ) );
		}

		$model = $this->getModel('products');

		$msg = $model->deleteproducts($cid);
		if(!$msg) {
			$msg = '';
			JError::raiseError(500, $model->getError());
		} else {
			$cache = &JFactory::getCache('com_awocoupon');
			$cache->clean();
		}

		//$mainframe->enqueueMessage($msg, 'message');
		$this->setRedirect( 'index.php?option=com_awocoupon&view=products',$msg );
	}

}