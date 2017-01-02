<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class AwoCouponViewProduct extends JView {

	function display($tpl = null) {
		global $mainframe, $option;
		
		$app =& JFactory::getApplication(); 
		$templateDir = JURI::base() . 'templates/' . $app->getTemplate(); 

		//create the toolbar
		JRequest::setVar('tmpl', 'component');
		$document	= & JFactory::getDocument();
		$document->setTitle( JText::_('PRODUCTS') );
		$document->addStyleSheet('components/com_awocoupon/assets/css/style.css');
		$document->addStyleSheet($templateDir.'/css/template.css');
		$document->addStyleSheet('templates/system/css/system.css');


		//initialise variables

		
		//get vars
		$id		= $mainframe->getUserStateFromRequest( $option.'.products.id', 	'id', 	JRequest::getVar( 'id' ), 'cmd' );

		//Get data from the model
		$row      	= & $this->get( 'Entry');
		$productlist = & $this->get('ProductList');
		
		//assign data to template
		$this->assignRef('id'			, $id);
		$this->assignRef('row'			, $row);
		$this->assignRef('productlist'	, $productlist);

		parent::display($tpl);
	}
}
?>