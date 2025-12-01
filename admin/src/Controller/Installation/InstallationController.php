<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

namespace AwoDev\Component\AwoCoupon\Administrator\Controller\Installation;

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

class InstallationController extends FormController {

	public function publishplugin() {
		$this->checkToken();
		
		$cid 	= JFactory::getApplication()->input->get( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel('installation');

			if(!$model->publish($cid, 1)) JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
			else {
				$this->setRedirect( 'index.php?option=com_awocoupon&view=installation', JText::_( 'COM_AWOCOUPON_MSG_ITEMS_PUBLISHED') );
			}
		}
	}
	public function unpublishplugin() {
		$this->checkToken();
		
		$cid 	= JFactory::getApplication()->input->get( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel('installation');

			if(!$model->publish($cid, 0)) JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
			else {
				$this->setRedirect( 'index.php?option=com_awocoupon&view=installation', JText::_( 'COM_AWOCOUPON_MSG_ITEMS_UNPUBLISHED') );
			}
		}
	}

}
