<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

defined('_JEXEC') or die('Restricted access');


function com_install(){ 
	$installer = new awocouponInstall('install');
	if(version_compare( JVERSION, '1.6.0', 'ge' )) $installer->install_tableupdatedj2();
	$installer->install_particulars(); 
}

function com_uninstall(){

	echo '<div><b>Database Tables Uninstallation: <font color="green">Successful</font></b></div>';

	$installer = new awocouponInstall('uninstall');
	$installer->uninstall_plugins();
}

class com_awocouponInstallerScript {

	function install($parent) {
		$installer = new awocouponInstall('install');
		$installer->install_particulars();
	}
 
	function update($parent) {
		$installer = new awocouponInstall('install');
		$installer->install_tableupdatedj2();
		$installer->install_particulars();

	}
	
	function uninstall($parent) {
		$installer = new awocouponInstall('uninstall');
		$installer->uninstall_plugins();
	}
	function preflight($type, $parent) {}
	function postflight($type, $parent) {}
}






class awocouponInstall {

	var $is_update = false;
	var $logger = array();
	var $is_debug = false;
	var $debug_file = 'awocoupon_install.xml';

	

	public function __construct($type) {
		if($type == 'install') {
			$xml_file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/awocoupon.xml';
			if(!file_exists($xml_file)) echo '<div><b>Database Tables Installation: <font color="green">Successful</font></b></div>';
			else {
				
				$this->is_update = true;
			}

			require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/awocoupon.config.php';
		}
		elseif($type=='uninstall') {
			
		}
		else {
			JError::raiseWarning(100, 'Invalid');
			JFactory::getApplication()->redirect('index.php?option=com_installer');
		}
	}

	
	
	function install_particulars() {
		if($this->is_debug) {
		// open file
			file_put_contents(JPATH_ROOT.'/tmp/'.$this->debug_file,'<?xml version="1.0" encoding="utf-8"?>'."\r\n\t".'<installation version="">'."\r\n");
		}
	

		$this->install_tableupdates();
		$this->install_migrate_vmcoupons();
		$this->install_plugins();
		
		$this->UPGRADE_2015();
			
		// Clear Caches
		$cache = JFactory::getCache();
		$cache->clean('com_awocoupon');
		
		
	}
	
	
	function install_tableupdatedj2() {
		// run install.mysql.sql file
		$db = JFactory::getDBO();
		$sqlfile = JPATH_ADMINISTRATOR.'/components/com_awocoupon/install.mysql.sql';
		// Don't modify below this line
		$buffer = file_get_contents($sqlfile);
		if ($buffer !== false) {
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);
			if (count($queries) != 0) {
				foreach ($queries as $query) {
					$query = trim($query);
					if ($query != '' && $query{0} != '#') {
						$db->setQuery($query);
						if (!$db->query()) {
							JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
							return false;
						}
					}
				}
			}
		}
	}

	function install_tableupdates() {
		$dbupgrades = array();
		if(!$this->_column_exists('#__'.AWOCOUPON,'function_type2')) {
		// upgrade to 2.0.9
			$dbupgrades[] = "ALTER TABLE #__".AWOCOUPON." ADD COLUMN `function_type2` enum('product','category') AFTER `function_type`;";
			$dbupgrades[] = "UPDATE #__".AWOCOUPON." SET `function_type2`='product';";
		}
	
		if(!empty($dbupgrades)) {
			$db			= JFactory::getDBO();
			//Apply Upgrades
			foreach ($dbupgrades AS $query) {
				$db->setQuery( $query );
				if(!$db->query()) {
				//Upgrade failed
					echo "<font color=red>".$dbupdate['message']." failed! SQL error:" . $db->stderr()."</font><br />";
				}
			}
			//Upgrade was successful
			echo "<div><b>Database Updates:</b> <font color=green>Upgrade Applied Successfully.</font></div>";			
		}

	}
	
	function install_migrate_vmcoupons() {
		if($this->is_update) return;
		
		$db			= JFactory::getDBO();

		$sql = 'INSERT INTO #__'.AWOCOUPON.' (coupon_code,num_of_uses,coupon_value_type,coupon_value,discount_type,function_type,min_value,published,startdate,expiration)
					SELECT coupon_code,IF(coupon_type="gift",1,0),percent_or_total,coupon_value,"overall",
							IF(coupon_type="gift","giftcert","coupon"),coupon_value_valid,IF(published=1,1,-1),
							coupon_start_date,coupon_expiry_date
					  FROM #__virtuemart_coupons';
		$db->setQuery($sql);
		if(!$db->query()) echo "<div><b>Import of Virtuemart coupons: <font color=red>Unsuccessful</font></b></div>";
		else echo "<div><b>Import of Virtuemart coupons: <font color=green>Successful</font></b></div>";

	}






	function install_plugins() {
	
		if(version_compare( JVERSION, '1.6.0', 'ge' )) {
			//$path		= $installer->getPath('manifest');
			//$version	= $installer->getManifest()->version;
			$installer	= JInstaller::getInstance();
			$manifest	= $installer->getManifest();
			$src = $installer->getPath('source');
		}
		else {
			//$sourcePath	= $installer->getPath('source');
			//$version	= $manifest->document->getElementByPath('version');
			$installer	= JInstaller::getInstance();
			$manifest	= $installer->getManifest();
			$manifest	= $manifest->document;
			$src 		= $installer->getPath('source');
		}
//echo '<pre>'; print_r($src); print_r($manifest); exit;

		// Install plugins
		$db = JFactory::getDBO();
		$is_success = false;
		if(version_compare( JVERSION, '1.6.0', 'ge' )) {
			$plugins = $manifest->xpath('plugins/plugin');
			foreach($plugins as $plugin){
				$plugin_attributes = current($plugin);
				if(empty($plugin_attributes['plugin'])) continue;
				
				$pname = $plugin_attributes['plugin'];
				$pgroup = $plugin_attributes['group'];
				$path = $src.'/plugins/'.$pgroup;
				$installer = new JInstaller;
				$result = $installer->install($path);
				$status->plugins[] = array('name'=>$pname,'group'=>$pgroup, 'result'=>$result);
				$db->setQuery('UPDATE #__extensions SET enabled=1 WHERE type="plugin" AND element='.$db->Quote($pname).' AND folder='.$db->Quote($pgroup));
				$db->query();
				$is_success = true;
			}
		}
		else {
			$plugins = $manifest->getElementByPath('plugins');
			if (is_a($plugins, 'JSimpleXMLElement') && count($plugins->children())) {
				foreach ($plugins->children() as $plugin) {
					$pname = $plugin->attributes('plugin');
					$pgroup = $plugin->attributes('group');
					$path = $src.DS.'plugins'.DS.$pgroup;
					$installer = new JInstaller;
					$result = $installer->install($path);
					$status->plugins[] = array('name'=>$pname,'group'=>$pgroup, 'result'=>$result);

					$db->setQuery('UPDATE #__plugins SET published=1 WHERE element='.$db->Quote($pname).' AND folder='.$db->Quote($pgroup));
					$db->query();
					$is_success = true;
				}
			}
		}
		if($is_success) echo '<div><b>Plugin Installation: <font color="green">Successful</font></b></div>';
	}
	

	function uninstall_plugins() {
		if(version_compare( JVERSION, '1.6.0', 'ge' )) {
			$installer	= JInstaller::getInstance();
			$manifest	= $installer->getManifest();
		}
		else {
			$installer	= JInstaller::getInstance();
			$manifest	= $installer->getManifest();
			$manifest	= $manifest->document;
		}

		$db = JFactory::getDBO();
		$is_success = false;
		if(version_compare( JVERSION, '1.6.0', 'ge' )) {
			$plugins = $manifest->xpath('plugins/plugin');
			foreach ($plugins as $plugin) {
				$plugin_attributes = current($plugin);
				if(empty($plugin_attributes['plugin'])) continue;

				$pname = $plugin_attributes['plugin'];
				$pgroup = $plugin_attributes['group'];
				$db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `type`="plugin" AND element='.$db->Quote($pname).' AND folder='.$db->Quote($pgroup));
				$ids = array_keys( $db->loadObjectList('extension_id') );
				if (count($ids)) {
					foreach ($ids as $id) {
						$installer = new JInstaller;
						$result = $installer->uninstall('plugin', $id);
					}
				}
				$is_success = true;
			}
		}
		else {
			$plugins = $manifest->getElementByPath('plugins');
			if (is_a($plugins, 'JSimpleXMLElement') && count($plugins->children())) {
				foreach ($plugins->children() as $plugin) {
					$pname = $plugin->attributes('plugin');
					$pgroup = $plugin->attributes('group');
					$db->setQuery('SELECT `id` FROM #__plugins WHERE element = '.$db->Quote($pname).' AND folder = '.$db->Quote($pgroup));
					$plugins = $db->loadResultArray();
					if (count($plugins)) {
						foreach ($plugins as $plugin) {
							$installer = new JInstaller;
							$result = $installer->uninstall('plugin', $plugin, 0);
						}
					}
					$is_success = true;
				}
			}
		}
		if($is_success) echo '<div><b>Plugin Uninstallation: <font color="green">Successful</font></b></div>';
	}

	function log($data) {
		$this->logger[] = $data;
		if($this->is_debug) file_put_contents(JPATH_ROOT.'/tmp/'.$this->debug_file,$data,FILE_APPEND);
	}
	
	
	
	function _column_exists($table,$column) {
		$db = JFactory::getDBO();
		$db->setQuery('DESC '.$table);
		$columns = $db->loadObjectList('Field');
		return isset($columns[$column]) ? true : false;
	}

	function UPGRADE_2015() {
		$file = JPATH_ADMINISTRATOR.'/components/com_awocoupon/admin.awocoupon.php';
		if(file_exists($file)) unlink($file);
	}
	
}








