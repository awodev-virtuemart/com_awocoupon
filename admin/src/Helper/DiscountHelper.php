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
use AwoDev\Component\AwoCoupon\Administrator\Helper\AwocouponHelper;

class DiscountHelper {

	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	var $cart = null;
	
	var $vmcoupon_code = '';
	var $vmcart = null;
	var $vmcartData = null;
	var $vmcartPrices = null;
	var $product_total = 0;

	var $error_msgs = array();

	public function __construct () {

		AwocouponHelper::instance()->boot();

		if ( ! class_exists( 'VirtueMartCart' ) ) {
			require JPATH_VM_SITE . '/helpers/cart.php';
		}
		$this->vmcart = \VirtueMartCart::getCart( false );

	  	$this->session = JFactory::getSession();

		if ( ! class_exists( 'VmVersion' ) ) {
			require JPATH_VM_ADMINISTRATOR . '/version.php';
		}			
		$this->vmversion = \VmVersion::$RELEASE;	
		if ( preg_match( '/\d/', substr( $this->vmversion, -1 ) ) == false ) {
			$this->vmversion = substr( $this->vmversion, 0, -1 );
		}
	}

	public function process_coupon_code( $code, & $data, & $prices ) {
		$this->vmcoupon_code = $code;
		$this->vmcartData = & $data;
		$this->vmcartPrices = & $prices;
		
		$coupon_session = $this->session->get( 'coupon', '', 'awocoupon' );
		if ( empty( $coupon_session ) ) {
			return false;
		}
		$coupon_session = unserialize( $coupon_session );
		if ( $coupon_session['uniquecartstring'] == $this->vm_getuniquecartstring( $coupon_session['coupon_code_db'], false ) ) {
			$this->finalize_coupon_vm( $coupon_session );
			return true;
		}

		$this->vmcart = \VirtueMartCart::getCart( false );
		
		return $this->validate_coupon_code();
	}
	
	public function cart_calculate_totals( & $data, & $prices ) {

		$coupon_session = $this->session->get( 'coupon', '', 'awocoupon' );
		if ( empty( $coupon_session ) ) {
			return;
		}

		$this->vmcartData =& $data;
		$this->vmcartPrices =& $prices;
		$this->validate_coupon_code();

		// get coupon session again, in case it has changed
		$coupon_session = $this->session->get( 'coupon', '', 'awocoupon' );
		if ( empty( $coupon_session ) ) {
			$this->delete_code_from_cart(); // need to clear variables in virtuemart such as couponCode
			return;
		}
		$coupon_session = unserialize( $coupon_session );

		$salesPriceCoupon = 0;
		$product_couponTax = 0;
		$product_couponValue = 0;
		if ( ! empty( $coupon_session['product_discount'] ) ) {
			$salesPriceCoupon += $coupon_session['product_discount'];

			$taxrate = $this->vmcartPrices['taxAmount'] / ( $this->vmcartPrices['salesPrice'] - $this->vmcartPrices['taxAmount'] );
			$product_couponTax = $coupon_session['product_discount'] - ( $coupon_session['product_discount'] / ( 1 + $taxrate ) );
			$product_couponValue = $coupon_session['product_discount'] - $product_couponTax;
		}

		$negative_multiplier = -1;

		$this->vmcartPrices['couponTax'] = $product_couponTax * $negative_multiplier;
		$this->vmcartPrices['couponValue'] = ( $product_couponValue - $this->vmcartPrices['couponTax'] ) * $negative_multiplier;
		$this->vmcartPrices['salesPriceCoupon'] = $salesPriceCoupon * $negative_multiplier;
		if ( isset( $this->vmcartPrices['billSub'] ) ) {
			$this->vmcartPrices['billSub'] += $this->vmcartPrices['couponValue'];
		}
		if ( isset( $this->vmcartPrices['billTaxAmount'] ) ) {
			$this->vmcartPrices['billTaxAmount'] += $this->vmcartPrices['couponTax'];
		}
		if ( isset( $this->vmcartPrices['billTotal'] ) ) {
			$this->vmcartPrices['billTotal'] += $this->vmcartPrices['salesPriceCoupon'];
		}
		return;
	}

	public function delete_code_from_cart() {
		$this->vmcart = \VirtueMartCart::getCart( false );
	  	return $this->initialize_coupon();
	}
	
	public function remove_coupon_code( $code ) {
		$this->vmcoupon_code = $code;
		return $this->cleanup_coupon_code( );
	}

	public function setCouponCode( $coupon_code, $cart = null, $is_auto = false ) {
		if ( empty( $cart ) ) {
			$cart = \VirtueMartCart::getCart();
		}
		if ( version_compare( $this->vmversion, '4.6.0', '<' ) ) {
			return $cart->setCouponCode( $coupon_code );
		}
		$cart->prepareCartData();

		$msg = $cart->validateCoupon( $coupon_code );

		if ( empty( $msg ) ) {
			$cart->couponCode = $coupon_code;
			$cart->cartData['couponCode'] = $coupon_code;

			$cart->prepareCartData( true );
			$cart->setCartIntoSession( true, true );
			$msg = \vmText::_( 'COM_VIRTUEMART_CART_COUPON_VALID' );
		}
		else {
			$cart->clearCoupon();
			$cart->prepareCartData( true );
			$cart->setCartIntoSession( true, true );
		}

		if ( $is_auto !== true && ! empty( $msg ) ) {
			vmInfo( $msg );
		}
		return $msg;
	}

	public function order_new( $order_id ) {

		$order_id = (int) $order_id;
		if ( $order_id < 1 ) {
			return null;
		}

		$coupon_session = $this->session->get( 'coupon', '', 'awocoupon' );
		if ( empty( $coupon_session ) ) {
			return null;
		}
		$coupon_session = unserialize( $coupon_session );

		// update virtuemart order coupon code
		$db = JFactory::getDBO();
		$db->setQuery( 'UPDATE #__virtuemart_orders SET coupon_code=' . $db->quote( $coupon_session['coupon_code_db'] ) . ' WHERE virtuemart_order_id=' . (int) $order_id );
		$db->execute();

		$this->cleanup_coupon_code( );
	}







	public function validating( $_code ) {
		$this->vmcoupon_code = $_code;
		$cart = \VirtueMartCart::getCart();
		if ( empty( $cart ) ) {
			return;
		}
		$cart->prepareCartData();
		$this->vmcart = $cart;
		$this->vmcartPrices = $cart->cartPrices;

		$rtn = $this->validate_coupon_code();

		if ( empty( $this->error_msgs ) ) {
		// success
			return '';
		}
		else {
		// error
			return implode( '<br />', $this->error_msgs );
		}
	}

	// function to process a coupon_code entered by a user
	public function validate_coupon_code() {
		if ( empty( $this->vmcart->products ) && ! empty( $this->vmcart->cartProductsData ) ) {
			return ; // the cart prices object has not yet been initialized
		}

		$db = JFactory::getDBO();	
		$submitted_coupon_code = trim( (string) $this->get_submittedcoupon() );

		// if cart is the same, do not reproccess coupon
		$awosess = $this->session->get( 'coupon', '', 'awocoupon' );
		if ( ! empty( $awosess ) ) {
			$awosess = unserialize( $awosess );
			if( 
				( 
					( ! empty( $submitted_coupon_code ) && $submitted_coupon_code == $awosess['coupon_code_db'] )
					|| empty( $submitted_coupon_code )
				)
				&& $awosess['uniquecartstring'] == $this->vm_getuniquecartstring( $awosess['coupon_code_db'], false )
			) {
				$this->finalize_coupon_vm( $awosess );
				return true;
			}
		}

		$this->initialize_coupon();
		
		if ( empty( $submitted_coupon_code ) && ! empty( $awosess['coupon_code_db'] ) ) {
			$submitted_coupon_code = $awosess['coupon_code_db'];
		}

		$current_date = gmdate( 'Y-m-d H:i:s' );
		$sql = 'SELECT id,coupon_code,num_of_uses,coupon_value_type,coupon_value,min_value,discount_type,function_type,function_type2
				  FROM #__' . AWOCOUPON . ' 
				 WHERE published=1
				   AND ( (startdate IS NULL 				AND expiration IS NULL) OR
						 (expiration IS NULL 				AND startdate<="' . $current_date . '") OR
						 (startdate IS NULL 				AND expiration>="' . $current_date . '") OR
						 (startdate<="' . $current_date . '"	AND expiration>="' . $current_date . '")
					   )
				   AND coupon_code=' . $db->Quote( $submitted_coupon_code );
		$db->setQuery( $sql );
		$coupon_row = $db->loadObject();
		$this->coupon_row = $coupon_row;
		if ( empty( $coupon_row ) ) {
		// no record, so coupon_code entered was not valid
			$this->return_false( 'errNoRecord' );
		}
		else {
		// coupon returned

			// retreive cart items
			$this->cart = new \stdclass();
			$this->cart->items = array();
			$this->cart->items_def = array();

			$this->product_total = 0;

			foreach ( $this->vmcart->products as $cartpricekey => $product ) {
				$productId = $product->virtuemart_product_id;
				if ( empty( $product->quantity ) || empty( $productId )){
					continue;
				}

				$this->cart->items_def[ $productId ] = array();
				$this->cart->items [] = array(
					'product_id' => $productId,
					'cartpricekey' => $cartpricekey,
					'discount' => empty( $this->vmcartPrices[ $cartpricekey ]['discountAmount']) ? 0 : $this->vmcartPrices[ $cartpricekey ]['discountAmount'],
					'product_price' => $this->vmcartPrices[ $cartpricekey ]['salesPrice'],
					'product_price_notax' => $this->vmcartPrices[ $cartpricekey ]['priceWithoutTax'],
					'product_price_tax' => $this->vmcartPrices[ $cartpricekey ]['salesPrice'] - $this->vmcartPrices[ $cartpricekey ]['priceWithoutTax'],
					'qty' => $product->quantity,
				);
				$this->product_total += $product->quantity * $this->vmcartPrices[ $cartpricekey ]['salesPrice'];
			}

			$return = $this->validate_coupon_code_helper ( $coupon_row );
			if ( ! empty( $return ) && $return['redeemed']) {
				if ( ! empty( $return['vmLogger_info_string'] ) ) {
					JFactory::getApplication()->enqueueMessage( $return['vmLogger_info_string'] );
				}
				return $this->finalize_coupon( $coupon_row, $return );
			};
		}

		$this->initialize_coupon();
		return false;
	}
	
	public function validate_coupon_code_helper( $coupon_row ) {
		$user = JFactory::getUser();
		$db = JFactory::getDBO();	
		$user_id = (int) $user->id;

		if ( empty( $coupon_row ) || empty( $this->cart->items ) || empty( $this->cart->items_def ) ) {
			return;
		}

		$_SESSION_coupon_discount = 0;
		$coupon_row->cart_items = $this->cart->items;
		$coupon_row->cart_items_def = $this->cart->items_def;

		// return user and product lists
		$coupon_row->userlist = $coupon_row->productlist = $coupon_row->categorylist = array();		
		$tmp = $db->setQuery( 'SELECT user_id FROM #__' . AWOCOUPON . '_user WHERE coupon_id=' . $coupon_row->id )->loadObjectList();
		foreach ( $tmp as $tmp2 ) {
			$coupon_row->userlist[ $tmp2->user_id ] = $tmp2->user_id;
		}

		// verify total is up to the minimum value for the coupon
		if ( ! empty( $coupon_row->min_value ) && round( $this->product_total, 2 ) < $coupon_row->min_value ) {
			return $this->return_false( 'errMinVal:' . $coupon_row->min_value );
		}

		if ( empty( $user_id ) && (	! empty( $coupon_row->userlist ) || ( $coupon_row->function_type == 'coupon' && $coupon_row->num_of_uses != 0 ) ) ) {
		// not a logged in user
			return $this->return_false( 'errUserLogin' );
		}
		
		// verify the user is on the list for this coupon
		if ( ! empty( $coupon_row->userlist ) ) {
			if ( ! isset( $coupon_row->userlist[ $user_id ] ) ) {
			// not on user list
				return $this->return_false( 'errUserNotOnList' );
			}
		}

		// number of use check
		if ( $coupon_row->num_of_uses != 0 ) {
			if ( $coupon_row->function_type == 'coupon' ) {
			// check to make sure user has not used it more than the limit
				$num = $db->setQuery( 'SELECT num FROM #__' . AWOCOUPON . '_history WHERE coupon_id=' . (int) $coupon_row->id . ' AND user_id=' . (int) $user_id )->loadResult();
				if ( ! empty( $num ) && $num >= $coupon_row->num_of_uses ) {
				// already used max number of times
					return $this->return_false( 'errUserMaxUse' );
				}
			}
			elseif( $coupon_row->function_type == 'giftcert' ) {
			// check to make sure giftcert has not been used more than the limit
				$num = $db->setQuery( 'SELECT SUM(num) FROM #__' . AWOCOUPON . '_history WHERE coupon_id=' . (int) $coupon_row->id . ' GROUP BY coupon_id' )->loadResult();
				if ( ! empty( $num ) && $num >= $coupon_row->num_of_uses ) {
				// already used max number of times
					return $this->return_false( 'errTotalMaxUse' );
				}
			}
		}

		// verify the product is on the list for this coupon
		if ( $coupon_row->function_type2 == 'product' ) {
			$tmp = $db->setQuery( 'SELECT product_id FROM #__' . AWOCOUPON . '_product WHERE coupon_id=' . (int) $coupon_row->id )->loadObjectList();
			foreach ( $tmp as $tmp2 ) {
				$coupon_row->productlist[ $tmp2->product_id ] = $tmp2->product_id;
			}
			if ( ! empty( $coupon_row->productlist ) ) {
				// inclusive list of products
				$is_in_list = false;
				foreach ( $coupon_row->cart_items as $row ) {
					if ( isset( $coupon_row->productlist[ $row['product_id'] ] ) ) {
						$is_in_list = true;
						break;
					}
				}
				if ( ! $is_in_list ) {
				// (include) not on product list
					return $this->return_false( 'errProductInclList' );
				}
			}
		}
		elseif ( $coupon_row->function_type2 == 'category' ) {
			$tmp = $db->setQuery( 'SELECT category_id FROM #__' . AWOCOUPON . '_category WHERE coupon_id=' . (int) $coupon_row->id )->loadObjectList();
			foreach ( $tmp as $tmp2 ) {
				$coupon_row->categorylist[ $tmp2->category_id ] = $tmp2->category_id;
			}

			// verify the category is on the list for this coupon
			if ( ! empty( $coupon_row->categorylist ) ) {
				// retreive the products in the order and their categories
				// get categories
				$ids_to_check = implode( ',', array_keys( $coupon_row->cart_items_def ) );
				$db->setQuery('
					SELECT virtuemart_category_id AS category_id,virtuemart_product_id AS product_id
					  FROM #__virtuemart_product_categories
					 WHERE virtuemart_product_id IN (' . $ids_to_check . ')
				' );
				$tmp = $db->loadObjectList();
				foreach ( $tmp as $tmp2 ) {
					$coupon_row->cart_items_def[ $tmp2->product_id ]['category'][ $tmp2->category_id ] = $tmp2->category_id;
				}
				// get category list of parent products
				$db->setQuery( '
					SELECT c.virtuemart_category_id AS category_id,p.virtuemart_product_id AS product_id
					  FROM #__virtuemart_products p 
					  JOIN #__virtuemart_product_categories c ON c.virtuemart_product_id=p.product_parent_id
					 WHERE p.virtuemart_product_id IN (' . $ids_to_check . ')
				' );
				$tmp = $db->loadObjectList();
				foreach ( $tmp as $tmp2 ) {
					$coupon_row->cart_items_def[ $tmp2->product_id ]['category'][ $tmp2->category_id ] = $tmp2->category_id;
				}

				// inclusive list of categories
				$is_in_list = false;
				foreach ( $coupon_row->cart_items as $row ) {
					$proid = $row['product_id'];
					foreach ( $coupon_row->cart_items_def[ $proid ]['category'] as $cat => $val ) {
						if ( isset( $coupon_row->categorylist[ $cat ] ) ) {
							$is_in_list = true;
							$coupon_row->cart_items_def[ $proid ]['is_valid_category'] = 1;
							//break 2;
						}
					}
				}
				if ( ! $is_in_list ) {
				// (include) not on category list
					return $this->return_false( 'errCategoryInclList' );
				}
			}
		}

		if ( $coupon_row->coupon_value_type == 'percent' ) {
		// percent calculation

			$coupon_value = 0;
			if ( $coupon_row->discount_type == 'overall' ) {
			// overall
				$coupon_value = round( $this->product_total * $coupon_row->coupon_value / 100, 2);
				if ( $this->product_total < $coupon_value ) {
					$coupon_value = (float) $this->product_total ;
				}
			} 
			else {
			//specific
				foreach ( $coupon_row->cart_items as $product_id => $row ) {
					$product_id = $row['product_id'];
					if( ( $coupon_row->function_type2 == 'product' && isset( $coupon_row->productlist[ $product_id ] ) )
					||  ( $coupon_row->function_type2 == 'category' && !empty( $coupon_row->cart_items_def[ $product_id ]['is_valid_category'] ) )
					) {
						$coupon_value += round( $row['qty'] * $row['product_price'] * $coupon_row->coupon_value / 100, 2);
					}
				}
				if ( $this->product_total < $coupon_value ) {
					$coupon_value = (float) $this->product_total;
				}
			}
			$_SESSION_coupon_discount = $coupon_value;
		} 
		else {
		// amount calculation

			$total = $this->product_total;
			if ( $coupon_row->discount_type == 'specific' ) {
				$total = 0;
				foreach ( $coupon_row->cart_items as $product_id => $row ) {
					$product_id = $row['product_id'];
					if ( ( $coupon_row->function_type2 == 'product' && isset( $coupon_row->productlist[ $product_id ] ) )
					||  ( $coupon_row->function_type2 == 'category' && !empty( $coupon_row->cart_items_def[ $product_id ]['is_valid_category'] ) )
					) {
						$total += $row['qty'] * $row['product_price'];
					}
				}
			}

			$coupon_value = $coupon_row->coupon_value;
			if ( $total < $coupon_value ) {
				$coupon_value = (float) $total;
			}
			$_SESSION_coupon_discount = $coupon_value;
		}

		if ( ! empty( $_SESSION_coupon_discount ) ) {
			return [
				'redeemed' => true,
				'coupon_id' => $coupon_row->id,
				'coupon_code' => $coupon_row->coupon_code,
				'product_discount' => $_SESSION_coupon_discount,
			];
		}
	}
	
	public function return_false( $key ) {
		$err = JText::_( 'COM_VIRTUEMART_COUPON_CODE_INVALID' );
		if ( substr( $key, 0, 10 ) == 'errMinVal:' ) {
			if ( ! class_exists( 'CurrencyDisplay' ) ) {
				require JPATH_VM_ADMINISTRATOR . '/helpers/currencydisplay.php';
			}
			$currency = CurrencyDisplay::getInstance();
			$coupon_value_valid = $currency->priceDisplay( substr( $key, 10 ) );
			$err = JText::_( 'COM_VIRTUEMART_COUPON_CODE_TOOLOW' ) . ' ' . $coupon_value_valid;
		}
		$this->error_msgs[] = $err;
		return;
	}

	public function initialize_coupon() {
		$this->session->set( 'coupon', 0, 'awocoupon' );

		// remove from vm session so coupon code is not called constantly
		$this->vmcart->couponCode = '';
		if ( isset( $this->vmcart->cartData ) ) {
			$this->vmcart->cartData['couponCode'] = '';
			$this->vmcart->cartData['couponDescr'] = '';
		}
		$this->vmcart->setCartIntoSession();
	}

	protected function get_submittedcoupon() {
		if ( empty( $this->vmcoupon_code ) ) {
			return '';
		}
		if ( $this->vmcoupon_code == JText::_( 'COM_VIRTUEMART_COUPON_CODE_CHANGE' ) ) {
			return '';
		}

		if ( $this->vmcoupon_code != strip_tags( $this->vmcoupon_code ) ) {
			return '';
		}

		$awosess = $this->session->get( 'coupon', '', 'awocoupon' );
		if ( ! empty( $awosess ) ) {
			$awosess = unserialize( $awosess );
			if ( ! empty( $awosess['coupon_code'] ) && $awosess['coupon_code'] == $this->vmcoupon_code ) {
				return '';
			}
		}

		return $this->vmcoupon_code; 
	}

	public function finalize_coupon( $coupon, $coupon_used ) {

		{
		// load coupon view
			$coupondelete_view = AwocouponHelper::instance()->get_view( 'coupondelete', 'html', 'Site', [
				'option' => 'com_awocoupon',
			] );
			$coupondelete_view->setLayout( 'default' );
			$coupondelete_view->coupons = [
				$coupon->id => [
					'text' => $coupon->coupon_code, 
					'link' => 'index.php?option=com_virtuemart&view=cart&task=deletecoupons&task2=deletecoupons&id=0',
				],
			];
			ob_start();
			$coupondelete_view->display();
			$html_coupon_code = ob_get_contents();
			ob_end_clean();
		}

		//update awocoupon variables
		$session_array = array(
			'redeemed' => true,
			'coupon_id' => $coupon->id,
			'coupon_code' => $html_coupon_code,
			'coupon_code_db' => $coupon->coupon_code,
			'product_discount' => $coupon_used['product_discount'],
			'uniquecartstring' => $this->vm_getuniquecartstring( $coupon->coupon_code ),
		);
		$this->session->set( 'coupon', serialize( $session_array ), 'awocoupon' );

		// update vm session so coupon code
		$this->vmcart->couponCode = $coupon->coupon_code;
		$this->vmcart->setCartIntoSession();

		$this->finalize_coupon_vm( $session_array );

		return true;
	}

	public function finalize_coupon_vm( $coupon_session ) {

		// update cart objects
		$this->vmcartData['couponCode'] = $coupon_session['coupon_code'];
		$this->vmcart->cartData['couponCode'] = $coupon_session['coupon_code'];
		$this->vmcartData['couponDescr'] = '';

		if ( version_compare( $this->vmversion, '4.6.4', '>=' ) ) {
			vRequest::setVar( 'token', JSession::getFormToken() );
		}
		return;

		//$salesPriceCoupon = 0;
		//$product_couponTax = 0;
		//$product_couponValue = 0;
		//if ( ! empty( $coupon_session['product_discount'] ) ) {
		//	$salesPriceCoupon += $coupon_session['product_discount'];
		//
		//	$taxrate = $this->vmcartPrices['taxAmount'] / ( $this->vmcartPrices['salesPrice'] - $this->vmcartPrices['taxAmount'] );
		//	$product_couponTax = $coupon_session['product_discount'] - ( $coupon_session['product_discount'] / ( 1 + $taxrate ) );
		//	$product_couponValue = $coupon_session['product_discount'] - $product_couponTax;
		//}
		//
		//$negative_multiplier = -1;
		//
		//$this->vmcartPrices['couponTax'] = $product_couponTax * $negative_multiplier;
		//$this->vmcartPrices['couponValue'] = ( $product_couponValue - $this->vmcartPrices['couponTax'] ) * $negative_multiplier;
		//$this->vmcartPrices['salesPriceCoupon'] = $salesPriceCoupon * $negative_multiplier;
		//if ( isset( $this->vmcartPrices['billSub'] ) ) {
		//	$this->vmcartPrices['billSub'] -= $this->vmcartPrices['couponValue'];
		//}
		//if ( isset( $this->vmcartPrices['billTaxAmount'] ) ) {
		//	$this->vmcartPrices['billTaxAmount'] -= $this->vmcartPrices['couponTax'];
		//}
		//if ( isset( $this->vmcartPrices['billTotal'] ) ) {
		//	$this->vmcartPrices['billTotal'] -= $this->vmcartPrices['salesPriceCoupon'];
		//}
	}

	public function vm_getuniquecartstring( $coupon_code = null, $is_setting = true ) {

		if ( empty( $coupon_code ) ) {
			@$coupon_code = $this->vmcart->couponCode;
		}
		if ( ! empty( $coupon_code ) ) {
			$string = $this->vmcartPrices['basePriceWithTax'] . '|' . $coupon_code;
			foreach ( $this->vmcart->products as $k => $r ) {
				$string .= '|' . $k . '|' . $r->quantity;
			}
			return $string . 'ship' . @ $this->vmcart->virtuemart_shipmentmethod_id;
		}
		return;
	}

    // function to remove coupon coupon_code from the database
	public function cleanup_coupon_code() {
	// remove the coupon coupon_code(s)

		$db = JFactory::getDBO();	
		$user 		= JFactory::getUser ();

		$coupon_session = $this->session->get( 'coupon', '', 'awocoupon' );
		if ( empty( $coupon_session ) ) {
			return null;
		}
		$coupon_session = unserialize( $coupon_session );
		$this->session->set( 'coupon', null, 'awocoupon' );
		$coupon_session['coupon_code'] = $coupon_session['coupon_code_db'];

		$coupon_row = $db->setQuery( 'SELECT id,function_type,num_of_uses FROM #__' . AWOCOUPON . ' WHERE published=1 AND id=' . (int) $coupon_session['coupon_id'] )->loadObject();
		if ( empty( $coupon_row ) ) {
			return null;
		}

		$test = $db->setQuery( 'SELECT coupon_id FROM #__' . AWOCOUPON . '_history WHERE coupon_id=' . (int) $coupon_row->id . ' AND user_id=' . (int) $user->id )->loadResult();
		if ( empty( $test ) ) {
			$db->setQuery( 'INSERT INTO #__' . AWOCOUPON . '_history (coupon_id,user_id,num) VALUES (' . (int) $coupon_row->id . ',' . (int) $user->id . ',1)' );
		}
		else {
			$db->setQuery( 'UPDATE #__' . AWOCOUPON . '_history SET num=num+1 WHERE coupon_id=' . (int) $coupon_row->id . ' AND user_id=' . (int) $user->id );
		}
		$db->execute();

		if ( ! empty( $coupon_row->num_of_uses ) ) {

			if ( $coupon_row->function_type == 'coupon' ) {
				// collect uses
				$coupon_row->userlist = array();		
				$tmp = $db->setQuery( 'SELECT user_id FROM #__' . AWOCOUPON . '_user WHERE coupon_id=' . (int) $coupon_row->id )->loadObjectList();
				foreach ( $tmp as $tmp2 ) {
					$coupon_row->userlist[ $tmp2->user_id ] = $tmp2->user_id;
				}

				if ( ! empty( $coupon_row->userlist ) ) {
				// limited amount of users so can be removed, cant remove if no users since new registration users can use coupon
					$tmp = $db->setQuery('SELECT user_id FROM #__' . AWOCOUPON . '_history WHERE coupon_id=' . (int) $coupon_row->id . ' AND num>=' . (int) $coupon_row->num_of_uses )->loadObjectList();
					$used_array = array();
					foreach ( $tmp as $tmp2 ) {
						$used_array[ $tmp2->user_id ] = $tmp2->user_id;
					}
					$diff = array_diff( $coupon_row->userlist, $used_array );
					if ( empty( $diff ) ) {
					// all users have used their coupons and can now be deleted
						$db->setQuery( 'UPDATE #__' . AWOCOUPON . ' SET published=-1 WHERE id=' . (int) $coupon_row->id );
						$db->execute();
					}
				}
			}
			elseif ( $coupon_row->function_type == 'giftcert' ) {
				// limited amount of users so can be removed, cant remove if no users since new registration users can use coupon
				$num = $db->setQuery( 'SELECT SUM(num) FROM #__' . AWOCOUPON . '_history WHERE coupon_id=' . (int) $coupon_row->id . ' GROUP BY coupon_id' )->loadResult();
				if ( ! empty( $num ) && $num >= $coupon_row->num_of_uses ) {
				// already used max number of times
					$db->setQuery( 'UPDATE #__' . AWOCOUPON . ' SET published=-1 WHERE id=' . $coupon_row->id );
					$db->execute();
				}
			}
		}

		return true;
	}



}

