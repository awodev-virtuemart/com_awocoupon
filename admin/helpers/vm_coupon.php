<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

class ps_coupon_process {

	// function to process a coupon_code entered by a user
	function process_coupon_code( $d ) {
		global $VM_LANG, $vmLogger, $my;

		$d['coupon_code'] = trim(vmGet( $_REQUEST, 'coupon_code' ));
		$coupon_id = vmGet( $_SESSION, 'coupon_id', null );
		
		
		$db = & JFactory::getDBO();	
		$current_date = JFactory::getDate(time(),JFactory::getConfig()->getValue ( 'offset' )*-1)->toFormat('%Y-%m-%d');
		$sql = 'SELECT id,coupon_code,num_of_uses,coupon_value_type,coupon_value,min_value,discount_type,function_type
				  FROM #__awocoupon 
				 WHERE published=1 AND (expiration IS NULL OR expiration="" OR expiration>="'.$current_date.'")
				   AND '.($coupon_id ? 'id='.(int)$coupon_id : 'coupon_code=\''.$d['coupon_code'].'\'');
		$db->setQuery( $sql );
		$coupon_row = $db->loadObjectList();
		$coupon_row = current($coupon_row);

		if(empty($coupon_row)) {
		// no record, so coupon_code entered was not valid
			return ps_coupon_process::return_false('no record, might be unpublished or expired');
		} 
		else {
		// coupon returned

			// initialize coupon
			unset(	$_SESSION['coupon_id'],
					$_SESSION['coupon_discount'],
					$_SESSION['coupon_redeemed'],
					$_SESSION['coupon_code'],
					$_SESSION['coupon_type']
				);
			if( empty( $d['total'])) {
			// we need some functions from the checkout module 
				require_once( CLASSPATH . "ps_checkout.php" );
				$checkout = new ps_checkout();
				$totals = $checkout->calc_order_totals($d);
				$d['total'] = $totals['order_subtotal']
							+ $totals['order_tax']
							+ $totals['order_shipping']
							+ $totals['order_shipping_tax']
							- $totals['payment_discount'];
			}

			//$user_id = (int)$_SESSION['auth']['user_id'];
			$user_id = (int)$my->id; //use $my to see if the user is logged in, not the temp userid (auth[user_id]) vm assigns for all users, logged in or not

			// return user and product lists
			$coupon_row->userlist = $coupon_row->productlist = array();		
			$sql = 'SELECT user_id FROM #__awocoupon_user WHERE coupon_id='.$coupon_row->id;
			$db->setQuery($sql);
			$tmp = $db->loadObjectList();
			foreach($tmp as $tmp2) $coupon_row->userlist[$tmp2->user_id] = $tmp2->user_id;

			$sql = 'SELECT product_id FROM #__awocoupon_product WHERE coupon_id='.$coupon_row->id;
			$db->setQuery($sql);
			$tmp = $db->loadObjectList();
			foreach($tmp as $tmp2) $coupon_row->productlist[$tmp2->product_id] = $tmp2->product_id;

			/*if(empty($user_id) && (	!empty($coupon_row->userlist) 
									|| ($coupon_row->function_type=='coupon' && $coupon_row->num_of_uses!=0)      )) {
			// not a logged in user
				return ps_coupon_process::return_false('user not loggedin');
			}*/
			if(empty($user_id)) {
				if(empty($d['enable_coupon_check_after_login'])
								&& (   !empty($coupon_row->userlist) 
									|| ($coupon_row->function_type=='coupon' && $coupon_row->num_of_uses!=0)      
									)) {
				// not a logged in user
					return ps_coupon_process::return_false('user not loggedin');
				}
			}


			// verify the user is on the list for this coupon
			if(!empty($coupon_row->userlist)) {
				if(!isset($coupon_row->userlist[$user_id])) {
				// not on user list
					return ps_coupon_process::return_false('not on user list');
				}
			}
		
			// number of use check
			if($coupon_row->num_of_uses!=0) {
				if($coupon_row->function_type=='coupon') {
				// check to make sure user has not used it more than the limit
					$sql = 'SELECT num FROM #__awocoupon_user_uses WHERE coupon_id='.$coupon_row->id.' AND user_id='.$user_id;
					$db->setQuery($sql);
					$num = $db->loadResult();
					if(!empty($num) && $num>=$coupon_row->num_of_uses) {
					// already used max number of times
						return ps_coupon_process::return_false('already used coupon max number of times');
					}
				} elseif($coupon_row->function_type=='giftcert') {
				// check to make sure giftcert has not been used more than the limit
					$sql = 'SELECT SUM(num) FROM #__awocoupon_user_uses WHERE coupon_id='.$coupon_row->id.' GROUP BY coupon_id';
					$db->setQuery($sql);
					$num = $db->loadResult();
					if(!empty($num) && $num>=$coupon_row->num_of_uses) {
					// already used max number of times
						return ps_coupon_process::return_false('already used giftcert max number of times');
					}
				}
			}
			
			// verify the product is on the list for this coupon
			if (!empty($coupon_row->productlist)) {
				$cart = $_SESSION['cart'];
				$is_in_list = false;

				for($i = 0; $i < $cart['idx']; $i++) {
					if (isset($coupon_row->productlist[$cart[$i]['product_id']])) {
						$is_in_list = true;
						break;
					}
				}
				if (!$is_in_list) {
					return ps_coupon_process::return_false('not on product list');
				}
			}

			// verify total is up to the minimum value for the coupon
			if (!empty($coupon_row->min_value) && round($d['total'],2)<$coupon_row->min_value) {
				return ps_coupon_process::return_false('minimum value not reached');
			}	

			if($coupon_row->coupon_value_type == 'percent') {
			// percent calculation

				$coupon_value = 0;
				if ($coupon_row->discount_type == 'overall') {
				// overall
					$coupon_value = round( $d["total"] * $coupon_row->coupon_value / 100, 2);
					if( $d["total"] < $coupon_value ) {
						$coupon_value = (float)$d['total'] ;
						$vmLogger->info( str_replace('{value}',$GLOBALS['CURRENCY_DISPLAY']->getFullValue( $coupon_value ),$VM_LANG->_('VM_COUPON_GREATER_TOTAL_SETTO')) );
					}

				} 
				else {
				//specific
					require_once(CLASSPATH.'ps_product.php');
					$ps_product= new ps_product;
					
					$cart = $_SESSION['cart'];
					for($i = 0; $i < $cart['idx']; $i++) {
						if (isset($coupon_row->productlist[$cart[$i]['product_id']])) {
							$price = $ps_product->get_adjusted_attribute_price($cart[$i]['product_id'], $cart[$i]['description']);
							
							// retrieve and add tax to product price
							$my_taxrate = $ps_product->get_product_taxrate($cart[$i]['product_id']);
							$product_price = round($price['product_price'] * (1+$my_taxrate),2);

							$product_price = $GLOBALS['CURRENCY']->convert( $product_price, @$price['product_currency'] );
							$coupon_calculation_value = $cart[$i]['quantity'] * $product_price;
							$coupon_value += round( $coupon_calculation_value * $coupon_row->coupon_value / 100, 2);
						}
					}

					if( $d['total'] < $coupon_value ) {
						$coupon_value = (float)$d['total'];
						$vmLogger->info( str_replace('{value}',$GLOBALS['CURRENCY_DISPLAY']->getFullValue( $coupon_value ),$VM_LANG->_('VM_COUPON_GREATER_TOTAL_SETTO')) );
					}
				}
				$_SESSION['coupon_discount'] = $coupon_value;

			} else {
			// amount calculation
				$total = $d['total'];
				if ($coupon_row->discount_type == 'specific') {
					$total = 0;
					require_once(CLASSPATH.'ps_product.php');
					$ps_product= new ps_product;
					
					$cart = $_SESSION['cart'];
					for($i = 0; $i < $cart['idx']; $i++) {
						if (isset($coupon_row->productlist[$cart[$i]['product_id']])) {
							$price = $ps_product->get_adjusted_attribute_price($cart[$i]['product_id'], $cart[$i]['description']);
							
							// retrieve and add tax to product price
							$my_taxrate = $ps_product->get_product_taxrate($cart[$i]['product_id']);
							$product_price = round($price['product_price'] * (1+$my_taxrate),2);
							$total += $cart[$i]['quantity'] * $product_price;
						}
					}

				}
				$coupon_value = $coupon_row->coupon_value;
				// Total Amount 
				if( $total < $coupon_value ) {
					$coupon_value = (float)$total ;
					$vmLogger->info( str_replace('{value}',$GLOBALS['CURRENCY_DISPLAY']->getFullValue( $coupon_value ),$VM_LANG->_('VM_COUPON_GREATER_TOTAL_SETTO')) );
				}
				$_SESSION['coupon_discount'] = $GLOBALS['CURRENCY']->convert( $coupon_value );
			}

			// mark this order as having used a coupon so people cant go and use coupons over and over */
			$_SESSION['coupon_redeemed'] = true;
			$_SESSION['coupon_id'] = $coupon_row->id;
			$_SESSION['coupon_code'] = $coupon_row->coupon_code;
			//$_SESSION['coupon_type'] = $coupon_row->num_of_uses==0 ? 'permanent' : 'gift';
			$_SESSION['coupon_type'] = 'gift'; // always call cleanup function
		}
	}    
	
	function return_false($message) {
		global $VM_LANG;
//echo '<pre>';exit( $message);

		$GLOBALS['coupon_error'] = $VM_LANG->_('PHPSHOP_COUPON_CODE_INVALID');
		unset(	$_SESSION['coupon_id'],
				$_SESSION['coupon_discount'],
				$_SESSION['coupon_redeemed'],
				$_SESSION['coupon_code'],
				$_SESSION['coupon_type']
			);
		
		return false;
	}

}


class ps_coupon_remove {

    // function to remove coupon coupon_code from the database
    function remove_coupon_code( &$d ) {
	// remove the coupon coupon_code(s)
	
		if( is_array($d['coupon_id'] )) {
			foreach( $d['coupon_id'] as $coupon ) ps_coupon_remove::process_coupon($coupon);
		}
		else {
			ps_coupon_remove::process_coupon($d['coupon_id']);
		}
		unset(	$_SESSION['coupon_id'],
				$_SESSION['coupon_discount'],
				$_SESSION['coupon_redeemed'],
				//$_SESSION['coupon_code'], need this commented to display coupon code in order
				$_SESSION['coupon_type']
			);
		$_SESSION['coupon_discount'] =    0;
		$_SESSION['coupon_redeemed']   = false;

		return true;
    }
    
	function process_coupon ($coupon_id) {
		$db = & JFactory::getDBO();	

		$sql = 'SELECT id,function_type,num_of_uses FROM #__awocoupon WHERE published=1 AND id='.(int)$coupon_id;
		$db->setQuery( $sql );
		$coupon_row = $db->loadObjectList();
		$coupon_row = current($coupon_row);
		
		if(!empty($coupon_row)) {
		// coupon found
		
			// mark coupon used
			$user_id = (int)$_SESSION['auth']['user_id'];
			$sql = 'SELECT num FROM #__awocoupon_user_uses WHERE coupon_id='.$coupon_row->id.' AND user_id='.$user_id;
			$db->setQuery($sql);
			$num = $db->loadResult();
			$sql = !empty($num) ? 'UPDATE #__awocoupon_user_uses SET num=num+1 WHERE coupon_id='.$coupon_row->id.' AND user_id='.$user_id
							  : 'INSERT INTO #__awocoupon_user_uses (coupon_id,user_id,num) VALUES ('.$coupon_row->id.','.$user_id.',1)';
			$db->setQuery( $sql );
			$db->query();
				
			if(!empty($coupon_row->num_of_uses)) {
				
				if($coupon_row->function_type == 'coupon') {
				// collect uses
					$coupon_row->userlist = array();		
					$sql = 'SELECT user_id FROM #__awocoupon_user WHERE coupon_id='.$coupon_row->id;
					$db->setQuery($sql);
					$tmp = $db->loadObjectList();
					foreach($tmp as $tmp2) $coupon_row->userlist[$tmp2->user_id] = $tmp2->user_id;

					if(!empty($coupon_row->userlist)) {
					// limited amount of users so can be removed, cant remove if no users since new registration users can use coupon
						$sql = 'SELECT user_id FROM #__awocoupon_user_uses WHERE coupon_id='.$coupon_row->id.' AND num>='.$coupon_row->num_of_uses;
						$db->setQuery($sql);
						$tmp = $db->loadObjectList();
						$used_array = array();
						foreach($tmp as $tmp2) $used_array[$tmp2->user_id] = $tmp2->user_id;
						$diff = array_diff($coupon_row->userlist,$used_array);
						if(empty($diff)) {
						// all users have used their coupons and can now be deleted
							$sql = 'UPDATE #__awocoupon SET published=-1 WHERE id='.$coupon_row->id;
							$db->setQuery( $sql );
							$db->query();
							/*
							$sql = 'DELETE FROM #__awocoupon_product WHERE coupon_id='.$coupon_row->id;
							$db->setQuery( $sql );
							$db->query();
							$sql = 'DELETE FROM #__awocoupon_user WHERE coupon_id='.$coupon_row->id;
							$db->setQuery( $sql );
							$db->query();
							$sql = 'DELETE FROM #__awocoupon_user_uses WHERE coupon_id='.$coupon_row->id;
							$db->setQuery( $sql );
							$db->query();
							$sql = 'DELETE FROM #__awocoupon WHERE id='.$coupon_row->id;
							$db->setQuery( $sql );
							$db->query();
							*/
						}
					}
				}
				elseif($coupon_row->function_type == 'giftcert') {
				// limited amount of users so can be removed, cant remove if no users since new registration users can use coupon
					$sql = 'SELECT SUM(num) FROM #__awocoupon_user_uses WHERE coupon_id='.$coupon_row->id.' GROUP BY coupon_id';
					$db->setQuery($sql);
					$num = $db->loadResult();
					if(!empty($num) && $num>=$coupon_row->num_of_uses) {
					// already used max number of times
						$sql = 'UPDATE #__awocoupon SET published=-1 WHERE id='.$coupon_row->id;
						$db->setQuery( $sql );
						$db->query();
					}
				}
			}
		}
 	}
}

