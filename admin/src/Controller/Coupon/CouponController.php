<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

namespace AwoDev\Component\AwoCoupon\Administrator\Controller\Coupon;

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\Input\Input;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Uri\Uri as JURI;
use Joomla\CMS\HTML\Helpers\Bootstrap as JHtmlBootstrap;
use Joomla\CMS\Editor\Editor as JEditor;
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Component\ComponentHelper as JComponentHelper;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Router\Route as JRoute;

class CouponController extends FormController {

	public function editscreen() {
		$cid 	= current( JFactory::getApplication()->input->get( 'cid', array(0), 'post', 'array' ) );
		$this->setRedirect( 'index.php?option='.AWOCOUPON_OPTION.'&view=coupon&layout=coupon&id=' . $cid );
	}

	public function goback() {	
		$this->checkToken();
		$this->setRedirect( 'index.php?option='.AWOCOUPON_OPTION.'&view=coupon' );
	}

	public function save( $key = NULL, $urlVar = NULL ) {
		$this->checkToken();

		//Sanitize
		$model = $this->getModel('coupon');
		$id = $model->save( JFactory::getApplication()->input->post->getArray() );
		if ( $id === false ) {
			JFactory::getApplication()->enqueueMessage($model->getError(), 'error');

			$view = $this->getView( 'coupon', 'html');
			$view->setLayout( 'coupon' );
			$view->setModel( $this->getModel ( 'coupon' ), true );
			$view->display();
			return false;
		}
	
		$this->setRedirect( 'index.php?option='.AWOCOUPON_OPTION.'&view=coupon', JText::_( 'COM_AWOCOUPON_MSG_DATA_SAVED' ) );
	}

	public function delete() {
		$this->checkToken();
		
		$cid 	= JFactory::getApplication()->input->get( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel('coupon');

			if(!$model->delete($cid)) JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
			else {
				$this->setRedirect( 'index.php?option='.AWOCOUPON_OPTION.'&view=coupon', count( $cid ).' '.JText::_('COM_AWOCOUPON_MSG_ITEMS_DELETED') );
			}
		}
	}

	public function publish() {
		$this->checkToken();
		
		$cid 	= JFactory::getApplication()->input->get( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel('coupon');

			if(!$model->publish($cid, 1)) JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
			else {
				$this->setRedirect( 'index.php?option='.AWOCOUPON_OPTION.'&view=coupon', JText::_( 'COM_AWOCOUPON_MSG_ITEMS_PUBLISHED') );
			}
		}
	}

	public function unpublish() {
		$this->checkToken();
		
		$cid 	= JFactory::getApplication()->input->get( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel('coupon');

			if(!$model->publish($cid, -1)) JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
			else {
				$this->setRedirect( 'index.php?option='.AWOCOUPON_OPTION.'&view=coupon', JText::_( 'COM_AWOCOUPON_MSG_ITEMS_UNPUBLISHED') );
			}
		}
	}


}
