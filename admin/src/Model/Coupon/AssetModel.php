<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

namespace AwoDev\Component\AwoCoupon\Administrator\Model\Coupon;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Table\Table;
use Joomla\Database\ParameterType;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Mail\MailHelper as JMailHelper;
use Joomla\CMS\Uri\Uri as JUri;
use Joomla\CMS\Router\Route as JRoute;
use Joomla\CMS\Component\ComponentHelper as JComponentHelper;
use AwoDev\Component\AwoCoupon\Administrator\Helper\AwocouponHelper;


class AssetModel extends ListModel {


	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'asset_id',
				'asset_name',
			);
		}

		parent::__construct($config);
	}


	protected function populateState( $ordering = 'asset_name', $direction = 'asc' ) {
		parent::populateState($ordering, $direction);
	}

	public function getListQuery() {
		$db = $this->getDbo();
		$query1 = $db->getQuery(true);
		$query2 = $db->getQuery(true);
		$coupon_id = JFactory::getApplication()->input->get( 'id' );

		if ( ! defined( 'VMLANG' ) ) {
			if ( ! class_exists( 'VmConfig' ) ) {
				require JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php';
			}
			\VmConfig::loadConfig();
		}

		// Create the base select statement.
		$query1->select( 'c.id,c.coupon_code,c.num_of_uses,c.coupon_value_type,c.coupon_value,
						 c.min_value,c.discount_type,c.function_type,c.expiration,pv.virtuemart_product_id as asset_id,lang.product_name as asset_name, "product" AS type' )
			->from( $db->quoteName( '#__' . AWOCOUPON, 'c' ) )
			->join( 'INNER', $db->quoteName( '#__' . AWOCOUPON . '_product', 'p' ) . ' ON p.coupon_id=c.id' )
			->join( 'INNER', $db->quoteName( '#__virtuemart_products', 'pv' ) . ' ON pv.virtuemart_product_id=p.product_id' )
			->join( 'INNER', $db->quoteName( '#__virtuemart_products_' . VMLANG, 'lang' ) . ' using (`virtuemart_product_id`)' )
			->where( 'c.id=' . (int) $coupon_id )
			->group( 'p.product_id' )
		;
		$query2->select( 'c.id,c.coupon_code,c.num_of_uses,c.coupon_value_type,c.coupon_value,
						 c.min_value,c.discount_type,c.function_type,c.expiration,
						 pv.virtuemart_category_id as asset_id,lang.category_name as asset_name, "category" AS type' )
			->from( $db->quoteName( '#__' . AWOCOUPON, 'c' ) )
			->join( 'INNER', $db->quoteName( '#__' . AWOCOUPON . '_category', 'p' ) . ' ON p.coupon_id=c.id' )
			->join( 'INNER', $db->quoteName( '#__virtuemart_categories', 'pv' ) . ' ON pv.virtuemart_category_id=p.category_id' )
			->join( 'INNER', $db->quoteName( '#__virtuemart_categories_' . VMLANG, 'lang' ) . ' using (`virtuemart_category_id`)' )
			->where( 'c.id=' . (int) $coupon_id )
			->group( 'p.category_id' )
		;

		$query1
			->union( $query2 )
			->order( $db->escape( $this->getState( 'list.ordering', 'asset_name' ) ) . ' ' . $db->escape( $this->getState( 'list.direction', 'ASC' ) ) )
		;

		return $query1;
	}


}
