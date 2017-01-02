<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * @package Joomla
 * @subpackage JomProducts
 * @since 1.0
 */
class AwoCouponModelAwoCoupon extends JModel
{
	var $_data = null;

	/**
	 * Constructor
	 **/
	function __construct() { parent::__construct(); }

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

		if (!@ini_get('safe_mode')) set_time_limit(180);
		
		if( empty( $version ) )
		{
			$parser		=& JFactory::getXMLParser('Simple');
	
			// Load the local XML file first to get the local version
			$xml		= JPATH_COMPONENT . DS . 'awocoupon.xml';
			
			$parser->loadFile( $xml );
			$document	=& $parser->document;
	
			$element		=& $document->getElementByPath( 'version' );
			$version		= $element->data();
		}
		return $version;
	}



	/**
	 * Method to get general stats
	 **/
	function getGeneralstats() {
		$_products = array();

		/*
		* Get total number of entries
		*/
		$query = 'SELECT count(id)  FROM #__awocoupon'; 
		$this->_db->SetQuery($query);
  		$_products['total'] = $this->_db->loadResult();

 		/*
		* Get total number of approved entries
		*/
		$query = 'SELECT count(id) FROM #__awocoupon WHERE published=1 AND (expiration IS NULL OR expiration="" OR expiration>="'.date('Y-m-d').'")'; 
		$this->_db->SetQuery($query);
  		$_products['active'] = $this->_db->loadResult();
		
		$query = 'SELECT count(id) FROM #__awocoupon WHERE published=-1 OR expiration<"'.date('Y-m-d').'"'; 
		$this->_db->SetQuery($query);
  		$_products['inactive'] = $this->_db->loadResult();
		
		/*$query = 'SELECT count(id) FROM #__awocoupon WHERE expiration<"'.date('Y-m-d').'"'; 
		$this->_db->SetQuery($query);
  		$_products['expired'] = $this->_db->loadResult();
		
		$query = 'SELECT count(id) FROM #__awocoupon WHERE published=-1'; 
		$this->_db->SetQuery($query);
  		$_products['unpublished'] = $this->_db->loadResult();
		*/
		return $_products;
  		
	}

	/**
	 * Method to get popular data
	 **/
	function getLastEntered() {
		$query = 'SELECT id,coupon_code,coupon_value_type,coupon_value FROM #__awocoupon ORDER BY id DESC LIMIT 5';
		$this->_db->SetQuery($query);
  		$hits = $this->_db->loadObjectList();
  		
  		return $hits;
	}
}
?>