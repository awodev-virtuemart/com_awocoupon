<?php
/*
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 */
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

class AwoCouponViewAwoCoupon extends JView {
	/**
	 * Creates the Entrypage
	 *
	 * @since 1.0
	 */
	function display( $tpl = null ) {
		global $mainframe;
		
		//Load pane behavior
		jimport('joomla.html.pane');

		//initialise variables
		$document	= & JFactory::getDocument();
		$pane   	= & JPane::getInstance('sliders');
		$template	= $mainframe->getTemplate();
		$params 	= & JComponentHelper::getParams('com_awocoupon'); // empty
		$update 	= 0;

		//build toolbar
		JToolBarHelper::title( 'AwoCoupon for Virtuemart', 'awocoupon' );

		//add css and submenu to document
		$document->addStyleSheet('components/com_awocoupon/assets/css/style.css');
		
		//Get data from the model
		$genstats 	= & $this->get( 'Generalstats' );
		$lastentered	= & $this->get( 'LastEntered' );

		$this->assignRef('genstats'		, $genstats);		
		$this->assignRef('lastentered'	, $lastentered);
		$this->assignRef('pane'			, $pane);
		$this->assignRef('update'		, $update);
		$this->assignRef('template'		, $template);

		parent::display($tpl);

	}
	
	/**
	 * Creates the buttons view
	 **/
	function addIcon( $image , $view, $text )
	{
		$lang		=& JFactory::getLanguage();
		$link		= 'index.php?option=com_awocoupon&view=' . $view;
?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="<?php echo $link; ?>">
					<?php echo JHTML::_('image', 'administrator/components/com_awocoupon/assets/images/'.$image.'.png' , NULL, NULL, $text ); ?>
					<span><?php echo $text; ?></span></a>
			</div>
		</div>
<?php
	}	

}
?>