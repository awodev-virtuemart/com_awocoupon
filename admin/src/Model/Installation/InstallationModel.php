<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

namespace AwoDev\Component\AwoCoupon\Administrator\Model\Installation;

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


class InstallationModel extends ListModel {

	public function getListQuery() {
	
		$sql = 'SELECT extension_id as id,name,element,folder,client_id,enabled,
						access,params,checked_out,checked_out_time,ordering
				  FROM #__extensions
				 WHERE type="plugin" AND element="awocoupon" AND folder IN ("vmcoupon","vmpayment")';

		return $sql;
	}

	public function publish( $cid = array(), $publish = 1 ) {
		$user 	= JFactory::getUser();

		if (count( $cid )) {
			$cids = implode( ',', $cid );

			$sql = 'UPDATE #__extensions SET enabled='.(int)$publish.' WHERE extension_id IN ('.$cids.')';
			$this->_db->setQuery( $sql );
		
			if (!$this->_db->execute()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return true;			
	}



}
