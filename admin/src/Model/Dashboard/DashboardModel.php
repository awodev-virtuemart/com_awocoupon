<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

namespace AwoDev\Component\AwoCoupon\Administrator\Model\Dashboard;

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


class DashboardModel extends ListModel {

	public function getGeneralstats() {
		$_products = array();

		$this->_db->SetQuery( 'SELECT count(id)  FROM #__'.AWOCOUPON );
  		$_products['total'] = $this->_db->loadResult();

		$current_date = AwocouponHelper::instance()->getDate(null,'Y-m-d H:i:s','utc2utc');
		$this->_db->SetQuery('
			SELECT count(id) 
			  FROM #__'.AWOCOUPON.' 
			 WHERE published=1
			   AND ( (startdate IS NULL 				AND expiration IS NULL) OR
					 (expiration IS NULL 				AND startdate<="'.$current_date.'") OR
					 (startdate IS NULL 				AND expiration>="'.$current_date.'") OR
					 (startdate<="'.$current_date.'"	AND expiration>="'.$current_date.'")
				   )
		');
  		$_products['active'] = $this->_db->loadResult();
		
		$this->_db->SetQuery( '
			SELECT count(id) 
			  FROM #__'.AWOCOUPON.' 
			 WHERE published=-1  OR startdate>"'.$current_date.'" OR expiration<"'.$current_date.'"
		');
  		$_products['inactive'] = $this->_db->loadResult();
		
		$this->_db->SetQuery( '
			SELECT count(id) 
			  FROM #__'.AWOCOUPON.' 
			 WHERE published=-2
		');
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
