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


class UserModel extends ListModel {


	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'uv.virtuemart_user_id',
				'uv.last_name',
				'uv.first_name',
			);
		}

		parent::__construct($config);
	}


	protected function populateState( $ordering = 'us.name', $direction = 'asc' ) {
		parent::populateState($ordering, $direction);
	}

	public function getListQuery() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$coupon_id = JFactory::getApplication()->input->get( 'id' );

		// Create the base select statement.
		$query->select( 'c.id,c.coupon_code,c.num_of_uses,c.coupon_value_type,c.coupon_value,
					 c.min_value,c.discount_type,c.function_type,c.expiration,u.user_id,
					 if(uv.virtuemart_user_id is NULL,us.name,uv.first_name) as first_name,uv.last_name' )
			->from( $db->quoteName( '#__' . AWOCOUPON, 'c' ) )
			->join( 'INNER', $db->quoteName( '#__' . AWOCOUPON . '_user', 'u' ) . ' ON u.coupon_id=c.id' )
			->join( 'INNER', $db->quoteName( '#__users', 'us' ) . ' ON us.id=u.user_id' )
			->join( 'LEFT', $db->quoteName( '#__virtuemart_userinfos', 'uv' ) . ' ON uv.virtuemart_user_id=u.user_id' )
			->where( 'c.id=' . (int) $coupon_id )
			->order( $db->escape( $this->getState( 'list.ordering', 'us.name' ) ) . ' ' . $db->escape( $this->getState( 'list.direction', 'ASC' ) ) )
			->group( 'u.user_id' )
		;

		return $query;
	}



}
