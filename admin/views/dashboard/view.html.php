<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponViewDashboard extends AwoCouponView {
	/**
	 * Creates the Entrypage
	 *
	 * @since 1.0
	 */
	function display( $tpl = null ) {
		global $def_lists;
		
		parent::display_beforeload();

		//Load pane behavior
		jimport('joomla.html.pane');

		//initialise variables
		$document	= JFactory::getDocument();
		$update 	= 0;

		//build toolbar
		JToolBarHelper::title( 'AwoCoupon Virtuemart', 'awocoupon' );
		
		
		//Get data from the model
		$genstats 	= $this->get( 'Generalstats' );
		$lastentered	= $this->get( 'LastEntered' );
		

		$this->assignRef('genstats'		, $genstats);		
		$this->assignRef('lastentered'	, $lastentered);
		$this->assignRef('update'		, $update);
		$this->assignRef('check'		, $check);
		$this->assignRef('def_lists'	, $def_lists);

		parent::display($tpl);

	}
	
	/**
	 * Creates the buttons view
	 **/
	function addIcon( $image , $view, $text )
	{
		$lang		= JFactory::getLanguage();
		$link		= 'index.php?option='.AWOCOUPON_OPTION.'&view=' . $view;
?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="<?php echo $link; ?>">
					<?php echo JHTML::_('image', com_awocoupon_ASSETS.'/images/'.$image.'.png' , NULL, NULL, $text ); ?>
					<span><?php echo $text; ?></span></a>
			</div>
		</div>
<?php
	}	

}
?>