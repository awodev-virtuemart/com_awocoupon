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

class AwoCouponViewUser extends JView {

	function display($tpl = null) {
		global $mainframe, $option;
		
		$app =& JFactory::getApplication(); 
		$templateDir = JURI::base() . 'templates/' . $app->getTemplate(); 

		//create the toolbar
		JRequest::setVar('tmpl', 'component');
		$document	= & JFactory::getDocument();
		$document->setTitle( JText::_('USERS') );
		$document->addStyleSheet('components/com_awocoupon/assets/css/style.css');
		$document->addStyleSheet($templateDir.'/css/template.css');
		$document->addStyleSheet('templates/system/css/system.css');


		//initialise variables

		$cid 		= JRequest::getVar( 'cid' );
		
		//get vars
		$id		= $mainframe->getUserStateFromRequest( $option.'.users.id', 	'id', 	JRequest::getVar( 'id' ), 'cmd' );

		//Get data from the model
		$row      	= & $this->get( 'Entry');
		$userlist = & $this->get('UserList');
		
		//assign data to template
		$this->assignRef('id'      	, $id);
		$this->assignRef('row'      	, $row);
		$this->assignRef('userlist'      , $userlist);

		parent::display($tpl);
	}
}
?>