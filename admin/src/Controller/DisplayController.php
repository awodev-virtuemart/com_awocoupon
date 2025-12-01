<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

namespace AwoDev\Component\AwoCoupon\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Uri\Uri as JURI;
use Joomla\CMS\HTML\Helpers\Bootstrap as JHtmlBootstrap;
use Joomla\CMS\Editor\Editor as JEditor;
use Joomla\CMS\HTML\HTMLHelper as JHtml;

class DisplayController extends BaseController {

	protected $default_view = 'dashboard';

	public function display( $cachable = false, $urlparams = array() ) {
		JHtml::_( 'jquery.framework' ); // load jquery
		JHtml::_( 'bootstrap.framework' );
		//JHtml::_( 'formbehavior.chosen', 'select:not(.nochosen)' );
		JFactory::getDocument()->addStyleSheet( com_awocoupon_ASSETS . '/css/style.css' );

		return parent::display();
	}

	public function users_x() {
		$db = JFactory::getDBO();
		$q = JFactory::getApplication()->input->get( 'term' );
		if(empty($q) || strlen($q)<2) exit;

		if ( $q == '___ALL___' ) {
			$db->setQuery( 'SELECT id,username as label FROM #__users ORDER BY username,id' );
		}
		else {
			$q = $db->Quote( $db->escape( trim( mb_strtolower( $q ) ), true ).'%', false );
			$db->setQuery( 'SELECT id,username as label FROM #__users WHERE username LIKE '.$q.' ORDER BY username LIMIT 25' );
		}

		$result = [];
		foreach ( $db->loadObjectList() as $row ) {
			$result[] = [
				'id' => $row->id,
				'label'=> $row->label,
				'value' => strip_tags( $row->label ),
			];
		}

		echo json_encode( $result );
		exit;
	}

	public function products_x() {
		$db = JFactory::getDBO();
		$q = JFactory::getApplication()->input->get( 'term' );
		if(empty($q) || strlen($q)<2) exit;

		if ( ! defined( 'VMLANG' ) ) {
			if ( ! class_exists( 'VmConfig' ) ) {
				require JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php';
			}
			\VmConfig::loadConfig();
		}

		if ( $q == '___ALL___' ) {
			$db->setQuery( '
				SELECT p.virtuemart_product_id AS id,CONCAT(lang.product_name," (",p.product_sku,")") AS label 
				  FROM #__virtuemart_products p
				  JOIN `#__virtuemart_products_'.VMLANG.'` as lang using (`virtuemart_product_id`)
				 WHERE p.published=1 ORDER BY label,p.product_sku
			' );
		}
		else {
			$q = $db->Quote( $db->escape( trim( mb_strtolower( $q ) ), true ).'%', false );
			$db->setQuery( '
				SELECT p.virtuemart_product_id AS id,CONCAT(lang.product_name," (",p.product_sku,")") AS label 
				  FROM #__virtuemart_products p
				  JOIN `#__virtuemart_products_'.VMLANG.'` as lang using (`virtuemart_product_id`)
				 WHERE p.published=1 AND CONCAT(lang.product_name," (",p.product_sku,")") LIKE '.$q.' ORDER BY label,p.product_sku LIMIT 25
			' );
		}

		$result = [];
		foreach ( $db->loadObjectList() as $row ) {
			$result[] = [
				'id' => $row->id,
				'label'=> $row->label,
				'value' => strip_tags( $row->label ),
			];
		}

		echo json_encode( $result );
		exit;
	}

	public function categorys_x() {
		$db = JFactory::getDBO();
		$q = JFactory::getApplication()->input->get( 'term' );
		if(empty($q) || strlen($q)<2) exit;

		if ( ! defined( 'VMLANG' ) ) {
			if ( ! class_exists( 'VmConfig' ) ) {
				require JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php';
			}
			\VmConfig::loadConfig();
		}

		if ( $q == '___ALL___' ) {
			$db->setQuery( '
				SELECT c.virtuemart_category_id AS id,lang.category_name AS label
				  FROM #__virtuemart_categories c
				  JOIN `#__virtuemart_categories_'.VMLANG.'` as lang using (`virtuemart_category_id`)
				 WHERE c.published=1 ORDER BY lang.category_name,c.virtuemart_category_id
			' );
		}
		else {
			$q = $db->Quote( $db->escape( trim( mb_strtolower( $q ) ), true ).'%', false );
			$db->setQuery( '
				SELECT c.virtuemart_category_id AS id,lang.category_name AS label
				  FROM #__virtuemart_categories c
				  JOIN `#__virtuemart_categories_'.VMLANG.'` as lang using (`virtuemart_category_id`)
				 WHERE c.published=1 AND lang.category_name LIKE '.$q.' ORDER BY lang.category_name,c.virtuemart_category_id LIMIT 25
			' );
		}

		$result = [];
		foreach ( $db->loadObjectList() as $row ) {
			$result[] = [
				'id' => $row->id,
				'label'=> $row->label,
				'value' => strip_tags( $row->label ),
			];
		}

		echo json_encode( $result );
		exit;
	}

}
