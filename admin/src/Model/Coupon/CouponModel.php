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


class CouponModel extends ListModel {


	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'c.id',
				'c.coupon_code',
				'c.function_type',
				'c.coupon_value_type',
				'c.coupon_value',
				'c.num_of_uses',
				'c.min_value',
				'c.discount_type',
				'c.startdate',
				'c.expiration',

				'discount_type',
				'coupon_value_type',
				'published',
			);
		}

		parent::__construct($config);
	}

	public function getItem( $id = null ) {
		$id = $id == null ? JFactory::getApplication()->input->get( 'id' ) : $id;
		if ( ! empty( $this->item ) && ( (int) $id < 1 || $this->item->id == $id ) ) {
			return $this->item;
		}
		$db = JFactory::getDBO();

		$this->item = $db->setQuery( 'SELECT * FROM #__' . AWOCOUPON . ' WHERE id=' . (int) $id )->loadObject();
		if ( empty( $this->item ) ) {
			return;
		}

		$this->item->userlist = [];
		$this->item->assetlist = [];	
		$this->item->num_of_uses_type = '';
		$this->item->asset1_function_type = $this->item->function_type2;
		if ( ! empty( $this->item->num_of_uses ) ) {
			$this->item->num_of_uses_type = $this->item->function_type == 'giftcert' ? 'total' : 'per_user';
		}
		else {
			$this->item->num_of_uses = '';
		}

		if ( ! empty( $this->item->startdate ) ) {
			$this->item->startdate = AwocouponHelper::instance()->getDate( $this->item->startdate, 'Y-m-d' );
		}
		if ( ! empty( $this->item->expiration ) ) {
			$this->item->expiration = AwocouponHelper::instance()->getDate( $this->item->expiration, 'Y-m-d' );
		}

		$tmp = $db->setQuery( 'SELECT user_id FROM #__' . AWOCOUPON . '_user WHERE coupon_id='. (int) $this->item->id )->loadObjectList();
		foreach( $tmp as $tmp2 ) {
			$this->item->userlist[ $tmp2->user_id ] = $tmp2->user_id;
		}

		if ( ! defined( 'VMLANG' ) ) {
			if ( ! class_exists( 'VmConfig' ) ) {
				require JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php';
			}
			\VmConfig::loadConfig();
		}

		if ( $this->item->function_type2 == 'product' ) {
			$this->item->assetlist = $db
				->setQuery( '
					SELECT a.coupon_id,a.product_id AS asset_id,c.product_name AS asset_name
					  FROM #__' . AWOCOUPON . '_product a
					  JOIN #__virtuemart_products b ON b.virtuemart_product_id=a.product_id
					  JOIN #__virtuemart_products_' . VMLANG . ' c USING (virtuemart_product_id)
					 WHERE a.coupon_id IN (' . (int)$this->item->id . ')
				' )
				->loadObjectList()
			;
		}
		elseif ( $this->item->function_type2 == 'category' ) {
			$this->item->assetlist = $db
				->setQuery( '
					SELECT a.coupon_id,a.category_id AS asset_id,c.category_name AS asset_name
					  FROM #__' . AWOCOUPON . '_category a
					  JOIN #__virtuemart_categories b ON b.virtuemart_category_id=a.category_id
					  JOIN #__virtuemart_categories_' . VMLANG . ' c USING (virtuemart_category_id)
					 WHERE a.coupon_id IN (' . (int) $this->item->id . ')
				' )
				->loadObjectList()
			;
		}
			
			
		$this->item->userlist = $db->setQuery('SELECT a.user_id,u.name as user_name FROM #__' . AWOCOUPON . '_user a JOIN #__users u ON u.id=a.user_id WHERE a.coupon_id=' . (int) $this->item->id )->loadObjectList();

		return $this->item;
	}

	public function getItems() {
		// Get a storage key.
		$store = $this->getStoreId('getItems');

		// Try to load the data from internal storage.
		if (!empty($this->cache[$store])) {
			return $this->cache[$store];
		}

		// Load the list items.
		$items = parent::getItems();

		// If empty or an error, just return.
		if (empty($items)) {
			return array();
		}

		$ids = '';
		$ptr = null;
		foreach ( $items as $i => $item ) {
			$ids .= $item->id.',';
			$ptr[ $item->id ]['usercount'] = & $items[ $i ]->usercount;
			$ptr[ $item->id ]['assetcount'] = & $items[ $i ]->assetcount;
			
			$items[ $i ]->num_of_uses_type = '';
			if ( ! empty( $items[ $i ]->num_of_uses ) ) {
				$items[ $i ]->num_of_uses_type = ( $items[ $i ]->function_type == 'giftcert') ? 'total' : 'per_user';
			}
		}
		if ( ! empty( $ids ) ) {
			$ids = substr( $ids, 0, -1 );
			$sql = 'SELECT coupon_id,count(user_id) as cnt FROM #__'.AWOCOUPON.'_user WHERE coupon_id IN ('.$ids.') GROUP BY coupon_id';
			$this->_db->setQuery( $sql );
			foreach($this->_db->loadObjectList() as $tmp) $ptr[$tmp->coupon_id]['usercount'] = $tmp->cnt;

			$sql = 'SELECT coupon_id,count(product_id) as cnt FROM #__'.AWOCOUPON.'_product WHERE coupon_id IN ('.$ids.') GROUP BY coupon_id';
			$this->_db->setQuery( $sql );
			foreach($this->_db->loadObjectList() as $tmp) @$ptr[$tmp->coupon_id]['assetcount'] = $tmp->cnt;

			$sql = 'SELECT coupon_id,count(category_id) as cnt FROM #__'.AWOCOUPON.'_category WHERE coupon_id IN ('.$ids.') GROUP BY coupon_id';
			$this->_db->setQuery( $sql );
			foreach($this->_db->loadObjectList() as $tmp) @$ptr[$tmp->coupon_id]['assetcount'] = $tmp->cnt;
			
		}

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}

	protected function populateState( $ordering = 'c.id', $direction = 'desc' ) {
		parent::populateState($ordering, $direction);
	}

	public function getListQuery() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select( 'c.id,c.coupon_code,c.num_of_uses,c.coupon_value_type,c.coupon_value,c.function_type2,
					 c.min_value,c.discount_type,c.function_type,c.startdate,c.expiration,c.published,0 as usercount,0 as productcount' )
			->from( $db->quoteName( '#__' . AWOCOUPON, 'c' ) )
			//->join( 'INNER', $db->quoteName( '#__virtuemart_products', 'p' ) . ' ON p.virtuemart_product_id=i.product_id' )
			->order( $db->escape( $this->getState( 'list.ordering', 'c.id' ) ) . ' ' . $db->escape( $this->getState( 'list.direction', 'DESC' ) ) )
			->group( 'c.id' )
		;

		// Filter: like / search
		$search = $this->getState('filter.search');
		if ( ! empty( $search ) ) {
			$search = $db->quote( '%' . str_replace(' ', '%', $db->escape( trim( $search ), true ) . '%' ) );
			$query->where( '(
				LOWER(c.coupon_code) LIKE ' . $search . '
			)');
		}
		$current_date = gmdate( 'Y-m-d H:i:s' );
		$state = $this->getState('filter.published');
		if ( ! empty( $state ) ) {
			if ( $state == 1 ) {
				$query->where( '
					c.published=1 
					   AND ( (c.startdate IS NULL 				AND c.expiration IS NULL) OR
							 (c.expiration IS NULL 				AND c.startdate<="'.$current_date.'") OR
							 (c.startdate IS NULL 				AND c.expiration>="'.$current_date.'") OR
							 (c.startdate<="'.$current_date.'"	AND c.expiration>="'.$current_date.'")
						   )
				' ); 
			}
			elseif ( $state == -1 ) {
				$query->where( '(c.published=-1 OR c.startdate>"'.$current_date.'" OR c.expiration<"'.$current_date.'")' );
			}
			else {
				$query->where( 'c.published=' . (int) $state );
			}
		}
		$coupon_value_type = $this->getState('filter.coupon_value_type');
		if ( ! empty( $coupon_value_type ) ) {
			$query->where( 'c.coupon_value_type=' . $db->quote( $coupon_value_type ) );
		}
		$discount_type = $this->getState('filter.discount_type');
		if ( ! empty( $discount_type ) ) {
			$query->where( 'c.discount_type=' . $db->quote( $discount_type ) );
		}
		$function_type = $this->getState('filter.function_type');
		if ( ! empty( $function_type ) ) {
			$query->where( 'c.function_type=' . $db->quote( $function_type ) );
		}

		return $query;
	}

	public function save( $data ) {
		$data['startdate'] = ! empty( $data['startdate'] ) ? AwocouponHelper::instance()->getDate( $data['startdate'].' 00:00:00', 'Y-m-d H:i:s', 'loc2utc' ) : null;
		$data['expiration'] = ! empty( $data['expiration'] ) ? AwocouponHelper::instance()->getDate( $data['expiration'].' 23:59:59', 'Y-m-d H:i:s', 'loc2utc' ) : null;
		$data['function_type'] = ! empty( $data['num_of_uses_type'] ) && $data['num_of_uses_type'] == 'total' && ! empty( $data['num_of_uses'] ) ? 'giftcert' : 'coupon';
		$data['function_type2'] = empty( $data['asset1_function_type'] ) ? null : $data['asset1_function_type'];

		$errors = $this->validate( $data );
		if ( ! empty( $errors ) ) {
			$this->setError( implode( '<br>', $errors ) );
			return false;
		}

		$db = JFactory::getDBO();
		$sql_set = [
			'coupon_code=' . $db->quote( $data['coupon_code'] ),
			'num_of_uses=' . (int) $data['num_of_uses'],
			'coupon_value_type=' . $db->quote( $data['coupon_value_type'] ),
			'coupon_value=' . (float) $data['coupon_value'],
			'min_value=' . ( ! empty( $data['min_value'] ) ? (float) $data['min_value'] : 'NULL' ),
			'discount_type=' . $db->quote( $data['discount_type'] ),
			'function_type=' . $db->quote( $data['function_type'] ),
			'function_type2=' . ( ! empty( $data['function_type2'] ) ? $db->quote( $data['function_type2'] ) : 'NULL' ),
			'startdate=' . ( ! empty( $data['startdate'] ) ? $db->quote( $data['startdate'] ) : 'NULL' ),
			'expiration=' . ( ! empty( $data['expiration'] ) ? $db->quote( $data['expiration'] ) : 'NULL' ),
			'published=' . (int) $data['published'],
		];

		if ( empty( $data['id'] ) ) {
			$db->setQuery( 'INSERT INTO #__' . AWOCOUPON . ' SET ' . implode( ',', $sql_set ) );
			$db->execute();
			$id = $db->insertid();
		}
		else {
			$id = $data['id'];
			$db->setQuery( 'UPDATE #__' . AWOCOUPON . ' SET ' . implode( ',', $sql_set ) . ' WHERE id=' . (int) $id );
			$db->execute();
		}

		// clean out the products/users tables
		$db->setQuery( 'DELETE FROM #__' . AWOCOUPON . '_user WHERE coupon_id = ' . (int) $id )->execute();
		$db->setQuery( 'DELETE FROM #__' . AWOCOUPON . '_product WHERE coupon_id = ' . (int) $id )->execute();
		$db->setQuery( 'DELETE FROM #__' . AWOCOUPON . '_category WHERE coupon_id = ' . (int) $id )->execute();
		
		//store products and users if chosen
		if ( ! empty( $data['userlist'] ) && is_array( $data['userlist'] ) ) {
			$insert_str = [];
			foreach ( $data['userlist'] as $tmp ) {
				$insert_str[] = '(' . (int) $id . ',' . $db->quote( (string) $tmp ) . ')';
			}
			if ( ! empty( $insert_str ) ) {
				$db->setQuery( 'INSERT INTO #__' . AWOCOUPON . '_user ( coupon_id, user_id ) VALUES ' . implode( ',', $insert_str ) );
				$db->execute();
			}
		}
		
		if ( ! empty( $data['assetlist'] ) && is_array( $data['assetlist'] ) ) {
			$insert_str = [];
			foreach ( $data['assetlist'] as $tmp ) {
				$insert_str[] = '(' . (int) $id . ',' . $db->quote( (string) $tmp ) . ')';
			}
			if ( ! empty( $insert_str ) ) {
				if ( $data['function_type2'] == 'product' ) {
					$db->setQuery( 'INSERT INTO #__' . AWOCOUPON . '_product (coupon_id, product_id) VALUES ' . implode( ',', $insert_str ) );
					$db->execute();
				}
				elseif ( $data['function_type2'] == 'category' ) {
					$db->setQuery( 'INSERT INTO #__' . AWOCOUPON . '_category (coupon_id, category_id) VALUES ' . implode( ',', $insert_str ) );
					$db->execute();
				}
			}
		}

		return $id;
	}

	public function validate( $data ) {
		$errors = array();
		$db = JFactory::getDBO();

		if ( ! empty( $data['id'] ) ) {
			$test = (int) $db->setQuery( 'SELECT id FROM #__' . AWOCOUPON . ' WHERE id=' . (int) $data['id'] )->loadResult();
			if ( $test < 1 ) {
				$errors[] = JText::_( 'ERROR' );
			}
		}
		if ( ! empty( $data['coupon_code'] ) ) {
			$test = (int) $db->setQuery( 'SELECT id FROM #__' . AWOCOUPON . ' WHERE coupon_code=' . $db->quote( $data['coupon_code'] ) . ( ! empty( $data['id'] ) ? ' AND id!=' . (int) $data['id'] : '' ) )->loadResult();
			if ( $test > 0 ) {
				$errors[] = JText::_( 'COM_AWOCOUPON_ERR_DUPLICATE_CODE' );
			}
		}

		if ( ! empty( $data['num_of_uses_type'] ) && $data['num_of_uses_type'] != 'total' && $data['num_of_uses_type'] != 'per_user' ) {
			$errors[] = '<br>'.JText::_( 'COM_AWOCOUPON_CP_NUMBER_USES_TYPE' ) . ': ' . JText::_( 'COM_AWOCOUPON_ERR_ENTER_VALID_VALUE' );
		}
		if ( ! empty( $data['num_of_uses'] ) && !ctype_digit( $data['num_of_uses'] ) ) {
			$errors[] = '<br>' . JText::_( 'COM_AWOCOUPON_CP_NUMBER_USES' ) . ': ' . JText::_( 'COM_AWOCOUPON_ERR_ENTER_VALID_VALUE' );
		}
		if ( ( ! empty( $data['num_of_uses_type'] ) && empty( $data['num_of_uses'] ) ) || ( empty( $data['num_of_uses_type'] ) && ! empty( $data['num_of_uses'] ) ) ) {
			$errors[] = '<br>' . JText::_( 'COM_AWOCOUPON_CP_NUMBER_USES' ) . ': ' . JText::_( 'COM_AWOCOUPON_ERR_ENTER_VALID_VALUE' );
		}


		
		if ( empty( $data['coupon_code'] ) ) {
			$errors[] = JText::_('COM_AWOCOUPON_CP_COUPON').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
		}
		if ( empty( $data['coupon_value_type'] ) || ! in_array( $data['coupon_value_type'], [ 'percent', 'total' ] ) ) {
			$errors[] = JText::_( 'COM_AWOCOUPON_CP_VALUE_TYPE' ) . ': ' . JText::_( 'COM_AWOCOUPON_ERR_ENTER_VALID_VALUE' );
		}
		if ( empty( $data['coupon_value'] ) || ! is_numeric( $data['coupon_value'] ) ) {
			$errors[] = JText::_( 'COM_AWOCOUPON_CP_VALUE') . ': ' . JText::_( 'COM_AWOCOUPON_ERR_ENTER_VALID_VALUE' );
		}
		if ( empty( $data['discount_type'] ) || ! in_array( $data['discount_type'], [ 'specific', 'overall' ] ) ) {
			$errors[] = JText::_( 'COM_AWOCOUPON_CP_DISCOUNT_TYPE' ) . ': ' . JText::_( 'COM_AWOCOUPON_ERR_ENTER_VALID_VALUE' );
		}
		if ( empty( $data['function_type'] ) || ! in_array( $data['function_type'], [ 'coupon', 'giftcert' ] ) ) {
			$errors[] = JText::_( 'COM_AWOCOUPON_CP_FUNCTION_TYPE' ) . ': ' . JText::_( 'COM_AWOCOUPON_ERR_ENTER_VALID_VALUE' );
		}
		if ( ! empty( $data['function_type2'] ) && ! in_array( $data['function_type2'], [ 'product', 'category' ] ) ) {
			$errors[] = JText::_( 'COM_AWOCOUPON_CP_ASSET' ) . ': ' . JText::_( 'COM_AWOCOUPON_ERR_ENTER_VALID_VALUE' );
		}
		if ( ! empty( $data['function_type'] ) && $data['function_type'] == 'coupon' && empty( $data['assetlist'] ) && $data['discount_type'] == 'specific' ) {
			$errors[] = JText::_( 'COM_AWOCOUPON_CP_ERR_ONE_SPECIFIC' );
		}

		$is_start = true;
		if ( ! empty( $data['startdate'] ) ) {
			if ( ! preg_match( "/^\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2}$/", $data['startdate'] ) ) {
				$is_start = false;
				$errors[] = JText::_( 'COM_AWOCOUPON_CP_DATE_START' ) . ': ' . JText::_( 'COM_AWOCOUPON_ERR_ENTER_VALID_VALUE' );
			}
			else {
				list( $dtmp, $ttmp ) = explode( ' ', $data['startdate'] );
				list( $Y, $M, $D ) = explode( '-', $dtmp );
				list( $h, $m, $s ) = explode( ':', $ttmp );
				if ( $Y > 2100 || $M > 12 || $D > 31 || $h > 23 || $m > 59 || $s > 59 ) {
					$is_start = false;
					$errors[] = JText::_( 'COM_AWOCOUPON_CP_DATE_START' ) . ': ' . JText::_( 'COM_AWOCOUPON_ERR_ENTER_VALID_VALUE' );
				}
			}
		}
		else {
			$is_start = false;
		}

		$is_end = true;
		if ( ! empty( $data['expiration'] ) ) {
			if ( ! preg_match( "/^\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2}$/", $data['expiration'] ) ) {
				$is_end = true;
				$errors[] = JText::_( 'COM_AWOCOUPON_CP_EXPIRATION') . ': ' . JText::_( 'COM_AWOCOUPON_ERR_ENTER_VALID_VALUE' );
			}
			else {
				list( $dtmp, $ttmp ) = explode( ' ', $data['expiration'] );
				list( $Y, $M, $D ) = explode( '-', $dtmp );
				list( $h, $m, $s ) = explode( ':', $ttmp );
				if ( $Y > 2100 || $M > 12 || $D > 31 || $h > 23 || $m > 59 || $s > 59 ) {
					$is_end = true;
					$errors[] = JText::_( 'COM_AWOCOUPON_CP_EXPIRATION' ) . ': ' . JText::_( 'COM_AWOCOUPON_ERR_ENTER_VALID_VALUE' );
				}
			}
		} else {
			$is_end = false;
		}
		if ( $is_start && $is_end ) {
			list( $dtmp, $ttmp ) = explode( ' ', $data['startdate'] );
			list( $Y, $M, $D ) = explode( '-', $dtmp );
			list( $h, $m, $s ) = explode( ':', $ttmp );
			$c1 = (int) $Y . $M . $D . '.' . $h . $m . $s;
			list( $dtmp, $ttmp ) = explode( ' ', $data['expiration'] );
			list( $Y, $M, $D ) = explode( '-', $dtmp );
			list( $h, $m, $s ) = explode( ':', $ttmp );
			$c2 = (int) $Y . $M . $D . '.' . $h . $m . $s;
			if ( $c1 > $c2 ) {
				$errors[] = JText::_( 'COM_AWOCOUPON_CP_DATE_START' ) . '/' . JText::_( 'COM_AWOCOUPON_CP_EXPIRATION' ) . ': ' . JText::_( 'COM_AWOCOUPON_ERR_ENTER_VALID_VALUE' );
			}
		}

		return $errors;
	}

	public function delete( $cids ) {		
		
		if ( empty( $cids ) || ! is_array( $cids ) ) {
			return false;
		}
		$cids = implode( ',', array_map( 'intval', $cids ) );

		$query = 'DELETE FROM #__'.AWOCOUPON.'_product WHERE coupon_id IN ('. $cids .')';
		$this->_db->setQuery( $query );
		if(!$this->_db->execute()) {
			JFactory::getApplication()->enqueueMessage($this->_db->getErrorMsg(), 'error');
			return false;
		}

		$query = 'DELETE FROM #__'.AWOCOUPON.'_category WHERE coupon_id IN ('. $cids .')';
		$this->_db->setQuery( $query );
		if(!$this->_db->execute()) {
			JFactory::getApplication()->enqueueMessage($this->_db->getErrorMsg(), 'error');
			return false;
		}

		$query = 'DELETE FROM #__'.AWOCOUPON.'_user WHERE coupon_id IN ('. $cids .')';
		$this->_db->setQuery($query);
		if(!$this->_db->execute()) {
			JFactory::getApplication()->enqueueMessage($this->_db->getErrorMsg(), 'error');
			return false;
		}

		$query = 'DELETE FROM #__'.AWOCOUPON.'_history WHERE coupon_id IN ('. $cids .')';
		$this->_db->setQuery($query);
		if(!$this->_db->execute()) {
			JFactory::getApplication()->enqueueMessage($this->_db->getErrorMsg(), 'error');
			return false;
		}

		$query = 'DELETE FROM #__'.AWOCOUPON.' WHERE id IN ('. $cids .')';
		$this->_db->setQuery($query);
		if(!$this->_db->execute()) {
			JFactory::getApplication()->enqueueMessage($this->_db->getErrorMsg(), 'error');
			return false;
		}

		return $this->_db->getAffectedRows().' '.JText::_('COM_AWOCOUPON_MSG_ITEMS_DELETED');
	}

	public function publish($cids = array(), $publish = 1) {
		if ( empty( $cids ) || ! is_array( $cids ) ) {
			return false;
		}
		$cids = array_map( 'intval', $cids );

		$query = 'UPDATE #__' . AWOCOUPON . ' SET published = '.(int)$publish.' WHERE id IN ('. implode( ',', $cids ) .')';
		$this->_db->setQuery( $query );
	
		if (!$this->_db->execute()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}	

}
