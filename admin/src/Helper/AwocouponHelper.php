<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

namespace AwoDev\Component\AwoCoupon\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Router\Route as JRoute;
use Joomla\CMS\Component\ComponentHelper as JComponentHelper;
use Joomla\CMS\HTML\Helpers\Sidebar as JHtmlSidebar;
use Joomla\CMS\Uri\Uri as JUri;
use Joomla\CMS\Mail\MailHelper as JMailHelper;
use Joomla\CMS\Plugin\PluginHelper as JPluginHelper;
use Joomla\Event\Dispatcher as JEventDispatcher;

class AwocouponHelper extends ContentHelper {

	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public static function addSubmenu($vName) {
	}

	public function boot() {
		if ( defined( 'AWOCOUPON' ) ) {
			return;
		}
		define( 'AWOCOUPON',    						'awocoupon_vm' );
		define( 'AWOCOUPON_OPTION',    					'com_awocoupon' );
		define( 'com_awocoupon_ASSETS',    				\Joomla\CMS\Uri\Uri::root() . 'administrator/components/com_awocoupon/helper/assets' );
	}

	public function get_model( $name, $prefix = '', array $config = [] ) {
		return JFactory::getApplication()->bootComponent('com_awocoupon')->getMVCFactory()->createModel( $name, $prefix, $config );
	}

	public function get_view( $name, $type = 'html', $prefix = '', array $config = [] ) {
		if ( empty( $config['base_path'] ) ) {
			$base_path = JPATH_ROOT;
			if ( strtolower( $prefix ) == 'administrator' || ( empty( $prefix ) && JFactory::getApplication()->isClient( 'administrator' ) ) ) {
				$base_path = JPATH_ADMINISTRATOR;
			}
			$config['base_path'] =  $base_path . '/components/com_awocoupon';
		}
		return JFactory::getApplication()->bootComponent('com_awocoupon')->getMVCFactory()->createView( $name, $prefix, $type, $config );
	}

	public function get_controller( $name, $prefix = '', array $config = [] ) {
		$app = JFactory::getApplication();
		return JFactory::getApplication()->bootComponent('com_awocoupon')->getMVCFactory()->createController( $name, $prefix, $config, $app, $app->input );
	}

	public function dbEscape($value,$extra=false) {
		$db = JFactory::getDBO();
		$value = $db->escape($value,$extra);
		
		return $value;
	}

	public function getDate($date=null, $format=null, $type='utc2loc') {
		
		if(empty($date)) $date = 'now';
		
		if(is_numeric($date)) $date = gmdate('c', $date);

		if(in_array($type, array('utc2loc','utc2utc'))) {
			if($type=='utc2loc') {
				$tz = true;
				$offset = null;
			}
			elseif($type=='utc2utc') {
				$tz = null;
				$offset = 0;
			}
			return JHTML::_('date',$date,$format,$tz);
		}
		elseif($type=='loc2utc') {
			$local = false;
			return JFactory::getDate($date,JFactory::getConfig()->get('offset'))->format($format,$local);
		}
	}

	public function def() {
		return array(
			'function_type' => array(
				'coupon'=>JText::_( 'COM_AWOCOUPON_CP_COUPON' ),
				'giftcert'=>JText::_( 'COM_AWOCOUPON_GC_GIFTCERT' ),
			),
			'function_type2' => array(
				'product'=>JText::_( 'COM_AWOCOUPON_CP_PRODUCTS' ),
				'category'=>JText::_( 'COM_AWOCOUPON_CP_CATEGORY' ),
			),
			'published' => array(
				'1'=>JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' ),
				'-1'=>JText::_( 'COM_AWOCOUPON_CP_UNPUBLISHED' ),
			),
			'coupon_value_type' => array(
				'percent'=>JText::_( 'COM_AWOCOUPON_CP_PERCENTAGE' ),
				'total'=>JText::_( 'COM_AWOCOUPON_CP_AMOUNT' ),
			),
			'discount_type' => array(
				'overall'=>JText::_( 'COM_AWOCOUPON_CP_OVERALL' ),
				'specific'=>JText::_( 'COM_AWOCOUPON_CP_SPECIFIC' ),
			),
			'num_of_uses_type' => array(
				'total'=>JText::_( 'COM_AWOCOUPON_GBL_TOTAL' ),
				'per_user'=>JText::_( 'COM_AWOCOUPON_CP_PER_CUSTOMER' ),
			),	
			
		);
	}

}

