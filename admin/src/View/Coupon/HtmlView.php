<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

namespace AwoDev\Component\AwoCoupon\Administrator\View\Coupon;


\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper as JToolBarHelper;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Uri\Uri as JUri;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use AwoDev\Component\AwoCoupon\Administrator\Helper\AwocouponHelper;

class HtmlView extends BaseHtmlView {

	public function display( $tpl = null ) {

		$this->document = JFactory::getDocument();

		$layout = $this->getLayout();

		if ( method_exists( $this, 'layout_' . $layout ) ) {
			$this->{ 'layout_' . $layout }();
		}
		else {
			$this->layout_coupons();
		}

		// Check for errors.
		if ( ! empty( $errors = $this->get( 'Errors' ) ) ) {
			throw new GenericDataException( implode("\n", $errors ), 500 );
		}

		// Display the template
		parent::display( $tpl );

    }

	private function layout_coupons() {

		//create the toolbar
		JToolBarHelper::title( JText::_( 'COM_AWOCOUPON_CP_COUPONS' ), 'coupons' );
		JToolBarHelper::publishList('coupon.publish');
		JToolBarHelper::unpublishList('coupon.unpublish');
		JToolBarHelper::divider();
		JToolBarHelper::addNew('coupon.editscreen');
		JToolBarHelper::editList('coupon.editscreen');
		JToolBarHelper::divider();
		JToolBarHelper::deleteList( JText::_( 'COM_AWOCOUPON_ERR_CONFIRM_DELETE' ),'coupon.delete');
		JToolBarHelper::spacer();

		//Get data from the model
		$this->rows      	= $this->get( 'Items');
		$this->pagination    = $this->get('Pagination');
		$this->state         = $this->get('State');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// table ordering
		$this->lists = [
			'order' => $this->escape( $this->state->get( 'list.ordering' ) ),
			'order_Dir' => $this->escape( $this->state->get( 'list.direction' ) ),
		];

		//assign data to template
		$this->def_lists = AwocouponHelper::instance()->def();

		$this->setLayout( 'coupon_list' );
	}

	private function layout_coupon() {
		
		$this->item     	= $this->get( 'Item' );
		$this->def_lists = AwocouponHelper::instance()->def();
		

		$post = JFactory::getApplication()->input->get('post');
		if ( $post ) {
			if(!empty($post['userlist'])) {
				$tmp = $post['userlist'];
				$post['userlist'] = array();
				foreach($tmp as $id) $post['userlist'][$id] = (object) array('user_id'=>$id,'user_name'=>$post['usernamelist'][$id]);
			} //else $post['productlist'] = array();
			if(!empty($post['assetlist'])) {
				$tmp = $post['assetlist'];
				$post['assetlist'] = array();
				foreach($tmp as $id) $post['assetlist'][$id] = (object) array('asset_id'=>$id,'asset_name'=>$post['assetnamelist'][$id]);
			} 
			$this->item = (object) array_merge((array) $this->item, (array) $post); //bind the db return and post
			//$this->row->bind($post);
		}

		JToolBarHelper::title( JText::_( 'COM_AWOCOUPON_CP_COUPON' ), 'edit' );
		JToolBarHelper::save('coupon.save');
		JToolBarHelper::divider();
		JToolBarHelper::cancel('coupon.goback');
		JToolBarHelper::spacer();

		$this->setLayout( 'coupon_edit' );
	}

	private function layout_users() {
		$this->setModel( AwocouponHelper::instance()->get_model( 'Coupon\User' ), true );

		$this->items      	= $this->get( 'Items' );
		$this->coupon = AwocouponHelper::instance()->get_model( 'Coupon' )->getItem();
		$this->state         = $this->get('State');
		$this->def_lists = AwocouponHelper::instance()->def();
		$this->pagination    = $this->get('Pagination');
		
		// table ordering
		$this->lists = [
			'order' => $this->escape( $this->state->get( 'list.ordering' ) ),
			'order_Dir' => $this->escape( $this->state->get( 'list.direction' ) ),
		];

		$this->setLayout( 'user_list' );
	}

	private function layout_assets() {
		$this->setModel( AwocouponHelper::instance()->get_model( 'Coupon\Asset' ), true );

		$this->items      	= $this->get( 'Items' );
		$this->coupon = AwocouponHelper::instance()->get_model( 'Coupon' )->getItem();
		$this->state         = $this->get('State');
		$this->def_lists = AwocouponHelper::instance()->def();
		$this->pagination    = $this->get('Pagination');
		
		// table ordering
		$this->lists = [
			'order' => $this->escape( $this->state->get( 'list.ordering' ) ),
			'order_Dir' => $this->escape( $this->state->get( 'list.direction' ) ),
		];

		$this->setLayout( 'asset_list' );
	}

}
