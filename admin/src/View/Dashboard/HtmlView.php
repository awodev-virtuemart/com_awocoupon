<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

namespace AwoDev\Component\AwoCoupon\Administrator\View\Dashboard;


\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper as JToolBarHelper;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Uri\Uri as JUri;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Router\Route as JRoute;
use AwoDev\Component\AwoCoupon\Administrator\Helper\AwocouponHelper;

class HtmlView extends BaseHtmlView {

	public function display( $tpl = null ) {

		$this->document = JFactory::getDocument();

		$layout = $this->getLayout();

		if ( method_exists( $this, 'layout_' . $layout ) ) {
			$this->{ 'layout_' . $layout }();
		}
		else {
			$this->layout_default();
		}

		// Check for errors.
		if ( \count( $errors = $this->get( 'Errors' ) ) ) {
			throw new GenericDataException( implode("\n", $errors ), 500 );
		}

		// Display the template
		parent::display( $tpl );

    }

	private function layout_default() {
		$this->genstats 	= $this->get( 'Generalstats' );
		$this->lastentered	= $this->get( 'LastEntered' );
		$this->def_lists = AwocouponHelper::instance()->def();

		JToolBarHelper::title( JText::_( 'COM_AWOCOUPON' ), 'awocoupon' );
		JToolbarHelper::preferences( 'com_awocoupon' );

		$this->setLayout( 'order_list' );
	}


	public function addIcon( $image , $view, $text, $layout = '' )
	{
		$lang		= JFactory::getLanguage();
		$link		= 'index.php?option=' . AWOCOUPON_OPTION . '&view=' . $view;
		if ( ! empty( $layout ) ) {
			$link .= '&layout=' . (string) $layout;
		}
		echo '
			<div style="float:' . ( $lang->isRTL() ? 'right' : 'left' ) . ';">
				<div class="icon">
					<a href="' . JRoute::_( $link ) . '">
						' . JHTML::_('image', com_awocoupon_ASSETS.'/images/'.$image.'.png' , NULL, NULL, $text ) . '
						<span>' . $text . '</span></a>
				</div>
			</div>
		';
	}	



}
