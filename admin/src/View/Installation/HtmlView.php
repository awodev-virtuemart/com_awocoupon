<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

namespace AwoDev\Component\AwoCoupon\Administrator\View\Installation;


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
		$this->rows      	= $this->get( 'Items' );

		JToolBarHelper::title( JText::_('COM_AWOCOUPON_FI_INSTALLATION_CHECK'), 'installation' );
		JToolBarHelper::publishList('Installation.publishplugin');
		JToolBarHelper::unpublishList('Installation.unpublishplugin');

		$this->setLayout( 'default' );
	}




}
