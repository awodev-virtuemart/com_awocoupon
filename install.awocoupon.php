<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

defined('_JEXEC') or die('Restricted access');

class com_awocouponInstallerScript {
	public function install( $parent ) {
		$installer = new awocouponInstall( 'install' );
		$installer->install_particulars();
	}
	public function update( $parent ) {
		$installer = new awocouponInstall( 'install' );
		$installer->install_particulars();

	}
	public function uninstall($parent) {
		$installer = new awocouponInstall( 'uninstall' );
		$installer->uninstall_particulars();
	}
	public function preflight($type, $parent) {
		if ( version_compare( JVERSION, '4.0.0', '<' ) ) {
			JFactory::getApplication()->enqueueMessage( sprintf( 'AwoCoupon Requires Joomla 4.x' ), 'error' );
			return false;
		}
		return true;
	}
	public function postflight($type, $parent) {}
}

class awocouponInstall {

	var $version_old = 0;
	var $is_update = false;

	public function __construct( $type ) {
		if ( $type == 'install' ) {
			if ( ! file_exists( JPATH_ADMINISTRATOR . '/components/com_awocoupon/awocoupon.xml' ) ) {
				echo '<div><b>Database Tables Installation: <font color="green">Successful</font></b></div>';
			}
			else {
				$this->is_update = true;
				$contents = file_get_contents( JPATH_ADMINISTRATOR . '/components/com_awocoupon/awocoupon.xml' );
				preg_match( '/\<version\>(.*?)\<\/version\>/i', $contents, $matches );
				$this->version_old = $matches[1];
			}
			if ( ! defined( 'AWOCOUPON' ) ) {
				define( 'AWOCOUPON',    						'awocoupon_vm' );
			}
		}
		elseif($type=='uninstall') {
		}
		else {
			JFactory::getApplication()->enqueueMessage( 'Invalid', 'error' );
			JFactory::getApplication()->redirect( 'index.php?option=com_installer' );
		}
	}

	public function install_particulars() {
		$this->install_sqlfile();
		$this->install_tableupgrades();
		$this->install_migrate_vmcoupons();
		$this->install_plugins();
			
		// Clear Caches
		$cache = JFactory::getCache();
		$cache->clean('com_awocoupon');

	}

	public function uninstall_particulars() {
		$this->uninstall_plugins();
	}

	private function install_tableupgrades() {
		if ( $this->is_update !== true ) {
			return;
		}

		$dbupgrades = array();
		if ( empty( $dbupgrades ) ) {
			return;
		}

		$db = JFactory::getDBO();
		foreach ( $dbupgrades as $query ) {
			$db->setQuery( $query );
			if ( ! $db->execute() ) {
				echo '<font color=red>' . $dbupdate['message'] . ' failed! SQL error:' . $db->stderr() . '</font><br />';
			}
		}
		//Upgrade was successful
		echo '<div><b>Database Updates:</b> <font color=green>Upgrade Applied Successfully.</font></div>';			
	}

	private function install_sqlfile() {
		if ( $this->is_update !== true ) {
			return;
		}

		$manifest	= JInstaller::getInstance()->getManifest();
		$file_path = (string) current( $manifest->xpath('install/sql/file') );
		$sqlfile = JPATH_ADMINISTRATOR.'/components/com_awocoupon/' . $file_path;
		if ( empty( $file_path ) || ! file_exists( $sqlfile ) ) {
			return;
		}

		$buffer = file_get_contents( $sqlfile );
		if ( empty( $buffer ) || $buffer === false ) {
			return;
		}

		$queries = JInstaller::splitSql( $buffer );
		if ( empty( $queries ) ) {
			return;
		}

		$db = JFactory::getDBO();
		foreach ( $queries as $query ) {
			$query = trim( $query );
			if ( ! empty( $query ) && substr( $query, 0, 1 ) !== '#' ) {
				$db->setQuery( $query );
				if ( ! $db->execute() ) {
					JFactory::getApplication()->enqueueMessage( JText::sprintf( 'JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr( true ) ), 'error' );
					return false;
				}
			}
		}
	}

	private function install_plugins() {
		$installer	= JInstaller::getInstance();
		$manifest	= $installer->getManifest();
		$src = $installer->getPath( 'source' );

		// Install plugins
		$db = JFactory::getDBO();
		$is_success = false;
		$plugins = $manifest->xpath( 'plugins/plugin' );
		foreach ( $plugins as $plugin ) {
			$plugin_attributes = current( (array) $plugin );
			if ( empty( $plugin_attributes['plugin'] ) ) {
				continue;
			}
			$pname = $plugin_attributes['plugin'];
			$pgroup = $plugin_attributes['group'];
			$path = $src . '/plugins/' . $pgroup;
			$installer = new JInstaller;
			$result = $installer->install( $path );
			$db->setQuery( 'UPDATE #__extensions SET enabled=1 WHERE type="plugin" AND element=' . $db->quote( $pname ) . ' AND folder=' . $db->quote( $pgroup ) );
			$db->execute();
			$is_success = true;
		}
		if ( $is_success ) {
			echo '<div><b>Plugin Installation: <font color="green">Successful</font></b></div>';
		}
	}

	private function uninstall_plugins() {
		$installer	= JInstaller::getInstance();
		$manifest	= $installer->getManifest();

		$db = JFactory::getDBO();
		$is_success = false;
		$plugins = $manifest->xpath( 'plugins/plugin' );
		foreach ( $plugins as $plugin ) {
			$plugin_attributes = current( $plugin );
			if ( empty( $plugin_attributes['plugin'] ) ) {
				continue;
			}
			$pname = $plugin_attributes['plugin'];
			$pgroup = $plugin_attributes['group'];
			$db->setQuery( 'SELECT `extension_id` FROM #__extensions WHERE `type`="plugin" AND element=' . $db->quote( $pname ) . ' AND folder=' . $db->quote( $pgroup ) );
			$ids = array_keys( $db->loadObjectList( 'extension_id' ) );
			if ( count( $ids ) ) {
				foreach ( $ids as $id ) {
					$installer = new JInstaller;
					$result = $installer->uninstall( 'plugin', $id );
				}
			}
			$is_success = true;
		}
		if ( $is_success ) {
			echo '<div><b>Plugin Uninstallation: <font color="green">Successful</font></b></div>';
		}
	}

	private function install_migrate_vmcoupons() {
		if ( $this->is_update === true ) {
			return;
		}
		$db = JFactory::getDBO();
		$db->setQuery('
			INSERT INTO #__' . AWOCOUPON . ' ( coupon_code, num_of_uses, coupon_value_type, coupon_value, discount_type, function_type, min_value, published, startdate, expiration )
			SELECT 	coupon_code
					, IF ( coupon_type = "gift", 1, 0 )
					, percent_or_total
					, coupon_value
					, "overall"
					, IF ( coupon_type = "gift", "giftcert", "coupon" )
					, coupon_value_valid
					, IF ( published = 1, 1, -1 )
					, coupon_start_date
					, coupon_expiry_date
			  FROM #__virtuemart_coupons
		' );
		if ( ! $db->execute() ) {
			echo '<div><b>Import of Virtuemart coupons: <font color=red>Unsuccessful</font></b></div>';
		}
		else {
			echo '<div><b>Import of Virtuemart coupons: <font color=green>Successful</font></b></div>';
		}
	}


}



