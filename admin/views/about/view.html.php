<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

class AwoCouponViewAbout extends JView {

	function display( $tpl = null ) {
		// Load tooltips
		JHTML::_('behavior.tooltip', '.hasTip');

		//add css to document
		$document	= & JFactory::getDocument();
		$document->addStyleSheet('components/com_awocoupon/assets/css/style.css');

		//create the toolbar
		JToolBarHelper::title( JText::_( 'ABOUT' ), 'awocoupon' );

		//Retreive version from install file
		$parser =& JFactory::getXMLParser('Simple');
		$parser->loadFile( JPATH_ADMINISTRATOR.DS."components".DS."com_awocoupon".DS.'awocoupon.xml' );
		$doc		=& $parser->document;
		
		$element	=& $doc->getElementByPath( 'version' );
		$version	= $element->data();
		
		$this->assign( 'version'	, $version );
		
		parent::display( $tpl );
	}
}