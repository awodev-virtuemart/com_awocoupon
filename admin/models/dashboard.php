<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined( '_JEXEC' ) or die( 'Restricted access' );


class AwoCouponModelDashboard extends AwoCouponModel {

	/**
	 * Method to get general stats
	 **/
	function getGeneralstats() {
		$_products = array();

		/*
		* Get total number of entries
		*/
		$sql = 'SELECT count(id)  FROM #__'.AWOCOUPON; 
		$this->_db->SetQuery($sql);
  		$_products['total'] = $this->_db->loadResult();

 		/*
		* Get total number of approved entries
		*/
		$current_date = date('Y-m-d H:i:s');
		$sql = 'SELECT count(id) 
				  FROM #__'.AWOCOUPON.' 
				 WHERE published=1
				   AND ( ((startdate IS NULL OR startdate="") 	AND (expiration IS NULL OR expiration="")) OR
						 ((expiration IS NULL OR expiration="") AND startdate<="'.$current_date.'") OR
						 ((startdate IS NULL OR startdate="") 	AND expiration>="'.$current_date.'") OR
						 (startdate<="'.$current_date.'"		AND expiration>="'.$current_date.'")
					   )
				'; 
		$this->_db->SetQuery($sql);
  		$_products['active'] = $this->_db->loadResult();
		
		$sql = 'SELECT count(id) 
				  FROM #__'.AWOCOUPON.' 
				 WHERE published=-1  OR startdate>"'.$current_date.'" OR expiration<"'.$current_date.'"'; 
		$this->_db->SetQuery($sql);
  		$_products['inactive'] = $this->_db->loadResult();
		
		$sql = 'SELECT count(id) 
				  FROM #__'.AWOCOUPON.' 
				 WHERE published=-2'; 
		$this->_db->SetQuery($sql);
  		$_products['templates'] = $this->_db->loadResult();
		
		return $_products;
  		
	}

	/**
	 * Method to get popular data
	 **/
	function getLastEntered() {
		$query = 'SELECT id,coupon_code,coupon_value_type,coupon_value,function_type FROM #__'.AWOCOUPON.' ORDER BY id DESC LIMIT 5';
		$this->_db->SetQuery($query);
  		$hits = $this->_db->loadObjectList();
  		
  		return $hits;
	}

	
	function getLocalBuild() {
		$versionString	= $this->getFullLocalVersion();
		$tmpArray		= explode( '.' , $versionString );
		
		if( isset($tmpArray[2]) )
		{
			return $tmpArray[2];
		}
		
		// Unknown build number.
		return 0;
	}
	function getLocalVersion() {
		$versionString	= $this->getFullLocalVersion();
		$tmpArray		= explode( '.' , $versionString );
		
		if( isset($tmpArray[0] ) && isset( $tmpArray[1] ) )
		{
			return doubleval( $tmpArray[0] . '.' . $tmpArray[1] ); 
		}
		return 0;
	}
	function getFullLocalVersion() {
		static $version		= '';

		if( empty( $version ) ) {
			$parser		= JFactory::getXMLParser('Simple');
	
			// Load the local XML file first to get the local version
			$xml		= JPATH_COMPONENT.'/awocoupon.xml';
			
			$parser->loadFile( $xml );
			$document	= $parser->document;
	
			$element		= $document->getElementByPath( 'version' );
			$version		= $element->data();
			$version 		= str_replace(array(' ','pro'),'',$version);
		}
		return $version;
	}



}
