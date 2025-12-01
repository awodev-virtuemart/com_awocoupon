<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

namespace AwoDev\Component\AwoCoupon\Administrator\View\About;


\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper as JToolBarHelper;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Uri\Uri as JUri;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\HTML\HTMLHelper as JHtml;

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
		if ( ! empty( $errors = $this->get( 'Errors' ) ) ) {
			throw new GenericDataException( implode("\n", $errors ), 500 );
		}

		// Display the template
		parent::display( $tpl );

    }

	private function layout_default() {
		$element = simplexml_load_file(JPATH_ADMINISTRATOR.'/components/com_awocoupon/awocoupon.xml');
		$this->version = (string)$element->version;

		JToolBarHelper::title( JText::_( 'COM_AWOCOUPON_AT_ABOUT' ), 'awocoupon' );
		JToolbarHelper::preferences( 'com_awocoupon' );

		$this->setLayout( 'default' );
	}




}
