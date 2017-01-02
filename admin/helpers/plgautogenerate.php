<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

class awoAutoGenerate  {

	function getCouponTemplates() {
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/awocoupon.config.php';
		$db = JFactory::getDBO();
		$sql = 'SELECT id,coupon_code FROM #__'.AWOCOUPON.' ORDER BY coupon_code,id';
		$db->setQuery($sql);
		return $db->loadObjectList();
	}
	
	function generateCoupon($coupon_id,$coupon_code=null,$expiration=null,$override_user=null) {
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/awocoupon.config.php';
		$db = JFactory::getDBO();
		$coupon_id = (int)$coupon_id;
		if(!is_null($override_user)) $override_user = trim($override_user);
		if(!is_null($expiration)) $expiration = trim($expiration);
		
		$sql = 'SELECT * FROM #__'.AWOCOUPON.' WHERE id='.$coupon_id;
		$db->setQuery($sql);
		$crow = $db->loadObject();
		if(empty($crow)) return false;  // template coupon does not exist
		
		if(empty($coupon_code)) $coupon_code = awoAutoGenerate::generateCouponCode();
		elseif(awoAutoGenerate::isCodeUsed($coupon_code)) $coupon_code = awoAutoGenerate::generateCouponCode();

		$db_expiration = !empty($crow->expiration) ? '"'.$crow->expiration.'"' : 'NULL';
		if(!empty($expiration) && ctype_digit($expiration)) {
			$db_expiration = '"'.date('Y-m-d',time()+(86400*(int)$expiration)).'"';
		}

		$sql = 'INSERT INTO #__'.AWOCOUPON.' ( coupon_code,num_of_uses,coupon_value_type,coupon_value,min_value,discount_type,function_type,function_type2,expiration,published )
				VALUES ("'.$coupon_code.'",
						'.$crow->num_of_uses.',
						"'.$crow->coupon_value_type.'",
						'.$crow->coupon_value.',
						'.(!empty($crow->min_value) ? $crow->min_value : 'NULL').',
						"'.$crow->discount_type.'",
						"'.$crow->function_type.'",
						"'.$crow->function_type2.'",
						'.$db_expiration.',
						1
					)';
		$db->setQuery($sql);
		$db->query();
		$gen_coupon_id = $db->insertid();
		
		if(!empty($override_user) && ctype_digit(trim($override_user))) {
			$sql = 'INSERT INTO #__'.AWOCOUPON.'_user ( coupon_id,user_id ) VALUES ( '.$gen_coupon_id.','.$override_user.' )';
			$db->setQuery($sql);
			$db->query();
		} else {
			awoAutoGenerate::populateTable(AWOCOUPON.'_user','user_id',$coupon_id,$gen_coupon_id);
		}
		awoAutoGenerate::populateTable(AWOCOUPON.'_product','product_id',$coupon_id,$gen_coupon_id);
		awoAutoGenerate::populateTable(AWOCOUPON.'_category','category_id',$coupon_id,$gen_coupon_id);
		
		$obj = null;
		$obj->coupon_id = $gen_coupon_id;
		$obj->coupon_code = $coupon_code;
		return $obj;
	}
	
	private function populateTable($table,$column,$coupon_id,$gen_coupon_id) {
		$db = JFactory::getDBO();
		$insert_str = '';

		$sql = 'SELECT * FROM #__'.$table.' WHERE coupon_id='.$coupon_id;
		$db->setQuery($sql);
		$rows = $db->loadObjectList();
		foreach($rows as $row) $insert_str .= '('.$gen_coupon_id.',"'.$row->$column.'"),';
		if(!empty($insert_str)) {
			$sql = 'INSERT INTO #__'.$table.' ( coupon_id,'.$column.' ) VALUES '.substr($insert_str,0,-1);
			$db->setQuery($sql);
			$db->query();
		}
		
	}
	
	private function generateCouponCode() {
		$salt = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
		do { $coupon_code = awoAutoGenerate::randomCode(rand(8,12),$salt); } while (awoAutoGenerate::isCodeUsed($coupon_code));
		return $coupon_code;
	}
	private function isCodeUsed($code) {
		$db = JFactory::getDBO();
			
		$sql = 'SELECT id FROM #__'.AWOCOUPON.' WHERE coupon_code="'.$code.'"';
		$db->setQuery( $sql );
		$id = $db->loadResult();
		
		if(empty($id)) return false;
		return true;
	}
	private function randomCode($length,$chars){
		$rand_id='';
		$char_length = strlen($chars);
		if($length>0) { for($i=1; $i<=$length; $i++) { $rand_id .= $chars[mt_rand(0, $char_length-1)]; } }
		return $rand_id;
	}	

}
