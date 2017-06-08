<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );


if (!function_exists('printr')) { function printr($a) { echo '<pre>'.print_r($a,1).'</pre>'; } }
if (!function_exists('printrx')) { function printrx($a) { echo '<pre>'.print_r($a,1).'</pre>'; exit; } }


class vm_coupon {
	var $cart = null;
	
	var $vmcoupon_code = '';
	var $vmcart = null;
	var $vmcartData = null;
	var $vmcartPrices = null;
	var $product_total = 0;

	var $is_validateprocess = false;
	var $error_msgs = array();

	public function __construct () {
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/awocoupon.config.php';
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awolibrary.php';

		if(!class_exists('VirtueMartCart')) require(JPATH_VM_SITE.'/helpers/cart.php');
		$this->vmcart = VirtueMartCart::getCart(false);
		
		$vmcart_class_vars = get_class_vars(get_class($this->vmcart));
		if(isset($vmcart_class_vars['_triesValidateCoupon']))
		{ // disable virtuemart coupon re-try limit of 8
			$this->vmcart->_triesValidateCoupon = array();
			$this->vmcart->setCartIntoSession();
		}
		
	  	$this->session = JFactory::getSession();


		if(!class_exists('VmVersion')) require JPATH_VM_ADMINISTRATOR.'/version.php'; 
		$this->vmversion = VmVersion::$RELEASE;	
		if(preg_match('/\d/',substr($this->vmversion,-1))==false) $this->vmversion = substr($this->vmversion,0,-1);

		if(version_compare($this->vmversion, '3.0.10', '>=')) $this->is_validateprocess = true;
	}

	public static function process_coupon_code( $code, &$data, &$prices ) {
		$instance = new vm_coupon();
		$instance->vmcoupon_code = $code;
		$instance->vmcartData =& $data;
		$instance->vmcartPrices =& $prices;
		
		if($instance->is_validateprocess) {
			$coupon_session = $instance->session->get('coupon', '', 'awocoupon');
			if(empty($coupon_session)) return false;
			
			$coupon_session = unserialize($coupon_session);
			if($coupon_session['uniquecartstring']==$instance->vm_getuniquecartstring($coupon_session['coupon_code_db'],false)) {
				$instance->finalize_coupon_vm($coupon_session);
				return true;
			}
		}

		if(!class_exists('VirtueMartCart')) require(JPATH_VM_SITE.'/helpers/cart.php');
		$instance->vmcart = VirtueMartCart::getCart(false);
		
		return $instance->validate_coupon_code();
	}
	
	public static function delete_code_from_cart() {
		$instance = new vm_coupon();
		if(!class_exists('VirtueMartCart')) require(JPATH_VM_SITE.'/helpers/cart.php');
		$instance->vmcart = VirtueMartCart::getCart(false);
		
	  	return $instance->initialize_coupon();
	}
	
	public static function remove_coupon_code( $code ) {
		$instance = new vm_coupon();
		$instance->vmcoupon_code = $code;
	
		return $instance->cleanup_coupon_code( );
	}

	public static function vm_ValidateCouponCode($_code) { 
		$instance = new vm_coupon();
		return $instance->validating($_code);
	}


	
	function validating($_code) {
		if($this->is_validateprocess) {
			$this->vmcoupon_code = $_code;
			if(!class_exists('VirtueMartCart')) require JPATH_ROOT.'/components/com_virtuemart/helpers/cart.php';
			$cart = VirtueMartCart::getCart();
			if(empty($cart)) return;

			$cart->prepareCartData();
			$this->vmcart = $cart;
			$this->vmcartPrices = $cart->cartPrices;

			$rtn = $this->validate_coupon_code();
			
			
			if(empty($this->error_msgs)) {
			// success
				return '';
			}
			else {
			// error
				return implode('<br />',$this->error_msgs);
			}
					
		}
		else {
			if (version_compare($this->vmversion, '3.0.9', '>=')) {
				$this->vmcart->_calculated = false;
				$this->vmcart->setCartIntoSession(true, true);
			}
			else $prices = $this->vmcart->getCartPrices();
			
			return '';
		}

	}

	// function to process a coupon_code entered by a user
	function validate_coupon_code() {
		if (version_compare($this->vmversion, '2.9.8', '>=')
		&& empty($this->vmcart->products)
		&& !empty($this->vmcart->cartProductsData)
		) return ; // the cart prices object has not yet been initialized

		
		$vmcart_class_vars = get_class_vars(get_class($this->vmcart));
		if(isset($vmcart_class_vars['_triesValidateCoupon']))
		{ // disable virtuemart coupon re-try limit of 8
			$this->vmcart->_triesValidateCoupon = array();
			$this->vmcart->setCartIntoSession();
		}

		$db = JFactory::getDBO();	
		if (version_compare($this->vmversion, '2.9.8', '>=') && $this->vmcoupon_code==JText::_('COM_VIRTUEMART_COUPON_CODE_CHANGE')) $this->vmcoupon_code = '';
		
		// if cart is the same, do not reproccess coupon
		$awosess = $this->session->get('coupon', '', 'awocoupon');
		if(!empty($awosess) ){
			$awosess = unserialize($awosess);
			if( 
				( 
					(!empty($this->vmcoupon_code) && $this->vmcoupon_code==$awosess['coupon_code_db'])
					|| empty($this->vmcoupon_code)
				)
				&& $awosess['uniquecartstring']==$this->vm_getuniquecartstring($awosess['coupon_code_db'],false)
			) {
				$this->finalize_coupon_vm($awosess);
				return true;
			}
		}

		
		$this->initialize_coupon();
		//JFactory::getApplication()->set('_messageQueue','');
		
		if(empty($this->vmcoupon_code) && !empty($awosess['coupon_code_db'])) $this->vmcoupon_code = $awosess['coupon_code_db'];
		
		//$current_date = date('Y-m-d');
		$current_date = awolibrary::getDate(null,'Y-m-d H:i:s','utc2utc');
		$sql = 'SELECT id,coupon_code,num_of_uses,coupon_value_type,coupon_value,min_value,discount_type,function_type,function_type2
				  FROM #__'.AWOCOUPON.' 
				 WHERE published=1
				   AND ( ((startdate IS NULL OR startdate="") 	AND (expiration IS NULL OR expiration="")) OR
						 ((expiration IS NULL OR expiration="") AND startdate<="'.$current_date.'") OR
						 ((startdate IS NULL OR startdate="") 	AND expiration>="'.$current_date.'") OR
						 (startdate<="'.$current_date.'"		AND expiration>="'.$current_date.'")
					   )
				   AND coupon_code='.$db->Quote( awolibrary::dbescape($this->vmcoupon_code)).'';
		$db->setQuery( $sql );
		$coupon_row = $db->loadObject();
		$this->coupon_row = $coupon_row;
		
		if(empty($coupon_row)) {
		// no record, so coupon_code entered was not valid
			$this->return_false('errNoRecord');
		} 
		else {
		// coupon returned
		
			// retreive cart items
			$this->cart = new stdclass();
			$this->cart->items = array();
			$this->cart->items_def = array();

			$this->product_total = 0;
			
			foreach ($this->vmcart->products as $cartpricekey=>$product){
				$productId = $product->virtuemart_product_id;
				if (empty($product->quantity) || empty( $productId )){
					continue;
				}
				//$variantmod = '';//$calculator->parseModifier($product->variant);
				//$cartpricekey = $productId.$variantmod;
				//exit($cartpricekey);
				
				$this->cart->items_def[$productId] = array();
				$this->cart->items [] = array(
					'product_id' => $productId,
					'cartpricekey' => $cartpricekey,
					'discount' => empty($this->vmcartPrices[$cartpricekey]['discountAmount']) ? 0 : $this->vmcartPrices[$cartpricekey]['discountAmount'],
					'product_price' => $this->vmcartPrices[$cartpricekey]['salesPrice'],
					'product_price_notax' => $this->vmcartPrices[$cartpricekey]['priceWithoutTax'],
					'product_price_tax' => $this->vmcartPrices[$cartpricekey]['salesPrice']-$this->vmcartPrices[$cartpricekey]['priceWithoutTax'],
					'qty' => $product->quantity,
				);
				$this->product_total += $product->quantity*$this->vmcartPrices[$cartpricekey]['salesPrice'];
			}

			
			
			$return = $this->validate_coupon_code_helper ( $coupon_row );
			if(!empty($return) && $return['redeemed']) {
				if(!empty($return['vmLogger_info_string'])) JFactory::getApplication()->enqueueMessage($return['vmLogger_info_string']);
				return $this->finalize_coupon($coupon_row,$return);
			};

		}
		$this->initialize_coupon();
		return false;
	}
	
	function validate_coupon_code_helper( $coupon_row ) {
		$user = JFactory::getUser();
		$db = JFactory::getDBO();	

		$user_id = (int)$user->id;
		
		if(empty($coupon_row)) return;
		if(empty($this->cart->items)) return;
		if(empty($this->cart->items_def)) return;
		
		$_SESSION_coupon_discount = 0;
		$coupon_row->cart_items = $this->cart->items;
		$coupon_row->cart_items_def = $this->cart->items_def;


		// return user and product lists
		$coupon_row->userlist = $coupon_row->productlist = $coupon_row->categorylist = array();		
		$sql = 'SELECT user_id FROM #__'.AWOCOUPON.'_user WHERE coupon_id='.$coupon_row->id;
		$db->setQuery($sql);
		$tmp = $db->loadObjectList();
		foreach($tmp as $tmp2) $coupon_row->userlist[$tmp2->user_id] = $tmp2->user_id;



		// verify total is up to the minimum value for the coupon
		if (!empty($coupon_row->min_value) && round($this->product_total,2)<$coupon_row->min_value) {
			return $this->return_false('errMinVal:'.$coupon_row->min_value);
		}	

		if(empty($user_id) && (	!empty($coupon_row->userlist) 
								|| ($coupon_row->function_type=='coupon' && $coupon_row->num_of_uses!=0)      )) {
		// not a logged in user
			return $this->return_false('errUserLogin');
		}
		
		// verify the user is on the list for this coupon
		if(!empty($coupon_row->userlist)) {
			if(!isset($coupon_row->userlist[$user_id])) {
			// not on user list
				return $this->return_false('errUserNotOnList');
			}
		}
	
		// number of use check
		if($coupon_row->num_of_uses!=0) {
			if($coupon_row->function_type=='coupon') {
			// check to make sure user has not used it more than the limit
				$sql = 'SELECT num FROM #__'.AWOCOUPON.'_history WHERE coupon_id='.$coupon_row->id.' AND user_id='.$user_id;
				$db->setQuery($sql);
				$num = $db->loadResult();
				if(!empty($num) && $num>=$coupon_row->num_of_uses) {
				// already used max number of times
					return $this->return_false('errUserMaxUse');
				}
			} elseif($coupon_row->function_type=='giftcert') {
			// check to make sure giftcert has not been used more than the limit
				$sql = 'SELECT SUM(num) FROM #__'.AWOCOUPON.'_history WHERE coupon_id='.$coupon_row->id.' GROUP BY coupon_id';
				$db->setQuery($sql);
				$num = $db->loadResult();
				if(!empty($num) && $num>=$coupon_row->num_of_uses) {
				// already used max number of times
					return $this->return_false('errTotalMaxUse');
				}
			}
		}

		// verify the product is on the list for this coupon
		if($coupon_row->function_type2=='product') {
			$sql = 'SELECT product_id FROM #__'.AWOCOUPON.'_product WHERE coupon_id='.$coupon_row->id;
			$db->setQuery($sql);
			$tmp = $db->loadObjectList();
			foreach($tmp as $tmp2) $coupon_row->productlist[$tmp2->product_id] = $tmp2->product_id;
			if (!empty($coupon_row->productlist)) {
				// inclusive list of products
				$is_in_list = false;
				foreach($coupon_row->cart_items as $row) {
					if (isset($coupon_row->productlist[$row['product_id']])) {
						$is_in_list = true;
						break;
					}
				}
				if (!$is_in_list) {
				// (include) not on product list
					return $this->return_false('errProductInclList');
				}
			}
		}
		elseif($coupon_row->function_type2=='category') {
			$sql = 'SELECT category_id FROM #__'.AWOCOUPON.'_category WHERE coupon_id='.$coupon_row->id;
			$db->setQuery($sql);
			$tmp = $db->loadObjectList();
			foreach($tmp as $tmp2) $coupon_row->categorylist[$tmp2->category_id] = $tmp2->category_id;


			// verify the category is on the list for this coupon
			if (!empty($coupon_row->categorylist)) {
				// retreive the products in the order and their categories
				// get categories
				$ids_to_check = implode(',',array_keys($coupon_row->cart_items_def));
				$sql = 'SELECT virtuemart_category_id AS category_id,virtuemart_product_id AS product_id
						  FROM #__virtuemart_product_categories
						 WHERE virtuemart_product_id IN ('.$ids_to_check.')';
				$db->setQuery($sql);
				$tmp = $db->loadObjectList();
				foreach($tmp as $tmp2) $coupon_row->cart_items_def[$tmp2->product_id]['category'][$tmp2->category_id] = $tmp2->category_id;
				// get category list of parent products
				$sql = 'SELECT c.virtuemart_category_id AS category_id,p.virtuemart_product_id AS product_id
						  FROM #__virtuemart_products p 
						  JOIN #__virtuemart_product_categories c ON c.virtuemart_product_id=p.product_parent_id
						 WHERE p.virtuemart_product_id IN ('.$ids_to_check.')';
				$db->setQuery($sql);
				$tmp = $db->loadObjectList();
				foreach($tmp as $tmp2) $coupon_row->cart_items_def[$tmp2->product_id]['category'][$tmp2->category_id] = $tmp2->category_id;
				
				// inclusive list of categories
				$is_in_list = false;
				foreach($coupon_row->cart_items as $row) {
					$proid = $row['product_id'];
					foreach($coupon_row->cart_items_def[$proid]['category'] as $cat=>$val) {
						if(isset($coupon_row->categorylist[$cat])){
							$is_in_list = true;
							$coupon_row->cart_items_def[$proid]['is_valid_category'] = 1;
							//break 2;
						}
					}
				}
				if (!$is_in_list) {
				// (include) not on category list
					return $this->return_false('errCategoryInclList');
				}
			}
		}
		
		
		
		
		if($coupon_row->coupon_value_type == 'percent') {
		// percent calculation

			$coupon_value = 0;
			if ($coupon_row->discount_type == 'overall') {
			// overall
				$coupon_value = round( $this->product_total * $coupon_row->coupon_value / 100, 2);
				if( $this->product_total < $coupon_value ) $coupon_value = (float)$this->product_total ;

			} 
			else {
			//specific
				
				foreach($coupon_row->cart_items as $product_id=>$row) {
					$product_id = $row['product_id'];
					if( ($coupon_row->function_type2=='product' && isset($coupon_row->productlist[$product_id]))
					||  ($coupon_row->function_type2=='category' && !empty($coupon_row->cart_items_def[$product_id]['is_valid_category']))
					) {
						$coupon_value += round( $row['qty'] * $row['product_price'] * $coupon_row->coupon_value / 100, 2);
					}
				}
				if( $this->product_total < $coupon_value ) $coupon_value = (float)$this->product_total;
			}
			$_SESSION_coupon_discount = $coupon_value;

		} 
		else {
		// amount calculation

			$total = $this->product_total;
			if ($coupon_row->discount_type == 'specific') {
				$total = 0;
				foreach($coupon_row->cart_items as $product_id=>$row) {
					$product_id = $row['product_id'];
					if( ($coupon_row->function_type2=='product' && isset($coupon_row->productlist[$product_id]))
					||  ($coupon_row->function_type2=='category' && !empty($coupon_row->cart_items_def[$product_id]['is_valid_category']))
					) {
						$total += $row['qty'] * $row['product_price'];
					}
				}
			}

			$coupon_value = $coupon_row->coupon_value;
			if( $total < $coupon_value ) $coupon_value = (float)$total ;
			$_SESSION_coupon_discount = $coupon_value;
		}
			
			
			
		if(!empty($_SESSION_coupon_discount)) {
			return array(	'redeemed'=>true,
							'coupon_id'=>$coupon_row->id,
							'coupon_code'=>$coupon_row->coupon_code,
							'product_discount'=>$_SESSION_coupon_discount,
						);
		}
			
	}
	
	function return_false($key) {
		if($this->is_validateprocess) {
			$err = JText::_('COM_VIRTUEMART_COUPON_CODE_INVALID');
			if(substr($key,0,10)=='errMinVal:') {
				if (!class_exists('CurrencyDisplay')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
				$currency = CurrencyDisplay::getInstance();
				$coupon_value_valid = $currency->priceDisplay(substr($key,10));
				$err = JText::_('COM_VIRTUEMART_COUPON_CODE_TOOLOW').' '.$coupon_value_valid;
			}
			$this->error_msgs[] = $err;
			
			return;
		}
		
		// strip out Virtuemart successful message
		$orig_messages = $messages = JFactory::getApplication()->getMessageQueue();
		foreach($messages as $k=>$message) {
			if($message['message']==JText::_('COM_VIRTUEMART_CART_COUPON_VALID')) {
				unset($messages[$k]);
			}
		}
		if($orig_messages != $messages) {
			$sessionqueue = $this->session->get('application.queue');
			if(!empty($sessionqueue)) $this->session->set('application.queue', empty($messages) ? null : $messages);
			JFactory::getApplication()->set('_messageQueue',empty($messages) ? array() : $messages);
			if (class_exists('ReflectionClass')) {
				$app = JFactory::getApplication(); 
				$appReflection = new ReflectionClass($app);
				$_messageQueue = $appReflection->getProperty('_messageQueue');
				$_messageQueue->setAccessible(true);
				$_messageQueue->setValue($app, empty($messages) ? array() : $messages);
			}

		}

		// display error to screen, if coupon is being set
		//$task = JRequest::getVar('task');
		//if($task == 'setcoupon') {
		$err = JText::_('COM_VIRTUEMART_COUPON_CODE_INVALID');
		if(substr($key,0,10)=='errMinVal:') {
			if (!class_exists('CurrencyDisplay')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
			$currency = CurrencyDisplay::getInstance();
			$coupon_value_valid = $currency->priceDisplay(substr($key,10));
			$err = JText::_('COM_VIRTUEMART_COUPON_CODE_TOOLOW').' '.$coupon_value_valid;
		}
		JFactory::getApplication()->enqueueMessage($err,'error');
		//}

		return;
	}
	
	function initialize_coupon() {
		$this->session->set('coupon', 0, 'awocoupon');
		
		// remove from vm session so coupon code is not called constantly
		$this->vmcart->couponCode = '';
		if(isset($this->vmcart->cartData)) {
			$this->vmcart->cartData['couponCode'] = '';
			$this->vmcart->cartData['couponDescr'] = '';
		}
		$this->vmcart->setCartIntoSession();
		
	}
	
	function finalize_coupon($coupon,$coupon_used) {
		

		$is_auto = false;
		$display_arr = array(
			$coupon->id=>array(
				'text'=>$coupon->coupon_code, 
				'link'=>'index.php?option=com_virtuemart&view=cart&task=deletecoupons&task2=deletecoupons&id=0',
			),
		);

			
		{ // load coupon view
			if(!class_exists( 'AwoCouponSiteController' )) require JPATH_ROOT.'/components/com_awocoupon/controller.php';
			//$frontcontroller = new AwoCouponSiteController();
			$frontcontroller = new AwoCouponSiteController( array('base_path'=>JPATH_ROOT.'/components/com_awocoupon') );
			$frontcontroller->addViewPath( JPATH_ROOT.'/components/com_awocoupon/views' );
			$coupondelete_view = $frontcontroller->getView( 'coupondelete', 'html' );
			$coupondelete_view->addTemplatePath( JPATH_ROOT.'/components/com_awocoupon/views/coupondelete/tmpl' );
			$template = JFactory::getApplication()->getTemplate();
			if($template) $coupondelete_view->addTemplatePath( JPATH_ROOT.'/templates/'.$template.'/html/com_awocoupon/coupondelete' );
		
			$coupondelete_view->coupons = $display_arr;
			ob_start();
			$coupondelete_view->display();
			$html_coupon_code = ob_get_contents();
			ob_end_clean();
		}
		
		
		//update awocoupon variables
		$session_array = array(
			'redeemed'=>true,
			'coupon_id'=>$coupon->id,
			'coupon_code'=>$html_coupon_code,
			'coupon_code_db'=>$coupon->coupon_code,
			'product_discount'=>$coupon_used['product_discount'],
			'uniquecartstring'=>$this->vm_getuniquecartstring($coupon->coupon_code),
		);
		$this->session->set('coupon', serialize($session_array), 'awocoupon');
		
		// update vm session so coupon code
		$this->vmcart->couponCode = $coupon->coupon_code;
		$this->vmcart->setCartIntoSession();

		$this->finalize_coupon_vm($session_array);
		
		return true;
		
	}
	
	function finalize_coupon_vm ($coupon_session) {

		require_once(JPATH_VM_ADMINISTRATOR.DS.'version.php'); 
		$vmversion = VmVersion::$RELEASE;	
		if(preg_match('/\d/',substr($vmversion,-1))==false) $vmversion = substr($vmversion,0,-1);

		// update cart objects
		$this->vmcartData['couponCode'] = $this->vmcart->cartData['couponCode'] = $coupon_session['coupon_code'];
		$this->vmcartData['couponDescr'] = '';
		
		$salesPriceCoupon = 0;
		$product_couponTax = 0;
		$product_couponValue = 0;
		if(!empty( $coupon_session['product_discount'])) {
			$salesPriceCoupon += $coupon_session['product_discount'];
			
			$taxrate = $this->vmcartPrices['taxAmount']/($this->vmcartPrices['salesPrice']-$this->vmcartPrices['taxAmount']);
			$product_couponTax = $coupon_session['product_discount'] - ($coupon_session['product_discount']/(1+$taxrate));
			$product_couponValue = $coupon_session['product_discount'] - $product_couponTax;
		}
		
		$negative_multiplier = version_compare($vmversion, '2.0.21', '>=') ? -1 : 1;
		
		$this->vmcartPrices['couponTax'] = $product_couponTax * $negative_multiplier;
		$this->vmcartPrices['couponValue'] = ($product_couponValue-$this->vmcartPrices['couponTax']) * $negative_multiplier;
		$this->vmcartPrices['salesPriceCoupon'] = $salesPriceCoupon * $negative_multiplier;
		if(isset($this->vmcartPrices['billSub'])) $this->vmcartPrices['billSub'] -= $this->vmcartPrices['couponValue'];
		if(isset($this->vmcartPrices['billTaxAmount'])) $this->vmcartPrices['billTaxAmount'] -= $this->vmcartPrices['couponTax'];
		if(isset($this->vmcartPrices['billTotal'])) $this->vmcartPrices['billTotal'] -= $this->vmcartPrices['salesPriceCoupon'];
				
	}
	
	
	
	function vm_getuniquecartstring($coupon_code=null,$is_setting = true) {

		if(empty($coupon_code)) @$coupon_code = $this->vmcart->couponCode;
		if(!empty($coupon_code)) {
			$string = $this->vmcartPrices['basePriceWithTax'].'|'.$coupon_code;
			foreach($this->vmcart->products as $k=>$r) $string .= '|'.$k.'|'.$r->quantity;
			return $string.'ship'.@$this->vmcart->virtuemart_shipmentmethod_id;
		}
		return;
	}


    // function to remove coupon coupon_code from the database
    function cleanup_coupon_code( ) {
	// remove the coupon coupon_code(s)

		$db = JFactory::getDBO();	
		$user 		= JFactory::getUser ();
		
		$coupon_session = $this->session->get('coupon', '', 'awocoupon');
		if(empty($coupon_session) ) return null;
		$coupon_session = unserialize($coupon_session);
		
		$this->session->set('coupon', null, 'awocoupon');
		
		$coupon_session['coupon_code'] = $coupon_session['coupon_code_db'];

		
		$sql = 'SELECT id,function_type,num_of_uses FROM #__'.AWOCOUPON.' WHERE published=1 AND id='.(int)$coupon_session['coupon_id'];
		$db->setQuery( $sql );
		$coupon_row = $db->loadObject();
		if(empty($coupon_row)) return null;
		
		
		
		$db->setQuery('SELECT coupon_id FROM #__'.AWOCOUPON.'_history WHERE coupon_id='.$coupon_row->id.' AND user_id='.$user->id);
		$tmp = $db->loadResult();
		$sql = !empty($tmp) 
					? 'UPDATE #__'.AWOCOUPON.'_history SET num=num+1 WHERE coupon_id='.$coupon_row->id.' AND user_id='.$user->id
					: 'INSERT INTO #__'.AWOCOUPON.'_history (coupon_id,user_id,num) VALUES ('.$coupon_row->id.','.$user->id.',1)';
		$db->setQuery( $sql );
		$db->query();
		
		if(!empty($coupon_row->num_of_uses)) {
			
			if($coupon_row->function_type == 'coupon') {
				// collect uses
				$coupon_row->userlist = array();		
				$db->setQuery('SELECT user_id FROM #__'.AWOCOUPON.'_user WHERE coupon_id='.$coupon_row->id);
				$tmp = $db->loadObjectList();
				foreach($tmp as $tmp2) $coupon_row->userlist[$tmp2->user_id] = $tmp2->user_id;

				if(!empty($coupon_row->userlist)) {
				// limited amount of users so can be removed, cant remove if no users since new registration users can use coupon
					$db->setQuery('SELECT user_id FROM #__'.AWOCOUPON.'_history WHERE coupon_id='.$coupon_row->id.' AND num>='.$coupon_row->num_of_uses);
					$tmp = $db->loadObjectList();
					$used_array = array();
					foreach($tmp as $tmp2) $used_array[$tmp2->user_id] = $tmp2->user_id;
					$diff = array_diff($coupon_row->userlist,$used_array);
					if(empty($diff)) {
					// all users have used their coupons and can now be deleted
						$db->setQuery( 'UPDATE #__'.AWOCOUPON.' SET published=-1 WHERE id='.$coupon_row->id );
						$db->query();
					}
				}
			}
			elseif($coupon_row->function_type == 'giftcert') {
				// limited amount of users so can be removed, cant remove if no users since new registration users can use coupon
				$db->setQuery('SELECT SUM(num) FROM #__'.AWOCOUPON.'_history WHERE coupon_id='.$coupon_row->id.' GROUP BY coupon_id');
				$num = $db->loadResult();
				if(!empty($num) && $num>=$coupon_row->num_of_uses) {
				// already used max number of times
					$db->setQuery( 'UPDATE #__'.AWOCOUPON.' SET published=-1 WHERE id='.$coupon_row->id );
					$db->query();
				}
			}
				
		}
		return true;
	}


}

