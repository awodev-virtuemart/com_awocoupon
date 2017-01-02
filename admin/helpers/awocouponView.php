<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');


if(version_compare( JVERSION, '3.0.0', 'ge' )) {
	class AwoCouponView extends JViewLegacy {
	
		function display($tpl=null) {
			parent::display($tpl);
		}
		
		function display_beforeload() {
			if(JFactory::getApplication()->isAdmin()) {
				JHtml::_('bootstrap.framework');
				$document	= JFactory::getDocument();
				$document->addStyleSheet(JURI::root(true).'/administrator/components/com_awocoupon/assets/css/style.css');
				
				$t1	= JRequest::getVar('tmpl','');
				$t2	= JRequest::getVar('no_html','');
				$t3	= JRequest::getVar('format','');
				
				//echo "t1: $t1 t2: $t2 t3: $t3";
				
				$test = false;
				if( (empty($t1) || $t1=='index')
				&& empty($t2)
				&& (empty($t3) || $t3!='raw')) {
					require_once JPATH_COMPONENT_ADMINISTRATOR.'/toolbar.awocoupon.php';
				}
			}
		}
	}
}
else {

	jimport( 'joomla.application.component.view');

	class AwoCouponView extends JView {
	
		function display($tpl=null) {
			parent::display($tpl);
		}

		function display_beforeload() {
			if(JFactory::getApplication()->isAdmin()) {
				$document	= JFactory::getDocument();
				$asset_path = JURI::root(true).'/administrator/components/com_awocoupon/assets';
				$document->addStyleSheet($asset_path.'/css/style.css');
				$document->addScript($asset_path.'/js/jquery.min.js');
			}
		}
	}
}
