<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
function is_installed() {
	$file = JPATH_SITE.'/administrator/components/com_virtuemart/classes/ps_coupon.php';

	if (!file_exists($file) || !is_writable($file)) return FALSE;
	$content = file_get_contents($file);
	
	if(strpos($content,	'"com_awocoupon".DS."assets".DS."virtuemart".DS."ps_coupon_process.php"')!==false) return true;
	return false;

}
function write_to_ps_coupon () {
	$file = JPATH_SITE.'/administrator/components/com_virtuemart/classes/ps_coupon.php';

	if (!file_exists($file) || !is_writable($file)) return FALSE;
	
	// get and replace content
	$content = file_get_contents($file);
	$patterns = array(
		'/(function\s+process_coupon_code\s*\(\s*[$]d\s*\)\s*[{]\s*)/i',
		'/(function\s+remove_coupon_code\s*\(\s*[&][$]d\s*\)\s*[{]\s*)/i',
		);
	$replacements = array(
		'$1return require_once (JPATH_ADMINISTRATOR.DS."components".DS."com_awocoupon".DS."assets".DS."virtuemart".DS."ps_coupon_process.php");'."\n",
		'$1return require_once (JPATH_ADMINISTRATOR.DS."components".DS."com_awocoupon".DS."assets".DS."virtuemart".DS."ps_coupon_remove.php");'."\n",
		);
	$count = 0;
	$new_content = preg_replace($patterns, $replacements, $content, -1, $count);
	//echo '<textarea cols="120" rows="35">'.$new_content.'</textarea>';
	if($count != 2) return FALSE;
	
	if (!@$handle = fopen($file, 'w')) return FALSE;

	// Write $somecontent to our opened file.
	if (fwrite($handle, $new_content) === FALSE) return FALSE;
	
	fclose($handle);
	return TRUE;

}
function com_install(){
	
	if(is_installed()) {
		echo "<font color=green>Upgrade Applied Successfully.</font><br />";	

	} else {
		echo '<div><b>Step 1: <font color="green">Database Installation Applied Successful</font></b></div>';


		if(write_to_ps_coupon()) {
			echo '<div><b>Step 2: <font color="green">Virtuemart Installation Applied Successful</font></b></div>';
		} else {
			echo '
			<div><b>Step 2: <font color="red">Virtuemart Installation Applied Unsuccessful</font></b></div>
			<style>
			.title {
				text-transform: uppercase;
				font-weight: bold;
				font-size: large;
				border-bottom:1px solid #bbbbbb;
				margin-bottom:15px;
			}
			.title2 {
				font-weight: bold;
				padding-top: 15px;
			}

			.desc {
				padding-left:10px;
				margin-bottom:35px;
			}
			.desc2 {
				padding-left:15px;
			}
			.codeparent {
				margin:20px;
				margin-top:5px;
				margin-left:40px;
			}
			.codephp {
				margin-bottom:2px;
				width:600px;
				font-size:x-small;
				text-align:right;
			}
			.code {
				margin: 0px;
				padding: 6px;
				border: 1px inset;
				width: 600px;
				height: 50px;
				text-align: left;
				overflow: auto;
				background-color:#eeeeee;
			}
			.toc {
				border:1px solid #bbbbbb;
				background-color:#eeeeee;
				padding:10px;
				margin-bottom:35px;
			}
			</style>
			<div class="desc2">
				Thank you for installing <b>AwoCoupon for Virtuemart</b>.  
				An error occured when trying to write to a file.  So in order to finish the 
				installation you have to follow the steps below.  
				This is also in the user guide.
				<br><br>
				This section requires the user to have access to the website files for edit.  
				You may need to use an ftp client to access the files. <br/>
				1. &nbsp; Open the file websiteroot/administrator/components/com_virtuemart/classes/ps_coupon.php<br/>
				2. &nbsp; Right after these lines (around line 158):<br/>
					<div class="codeparent">
						<div class="codephp" >PHP Code:</div>
						<div class="code">
							<code style="white-space:nowrap">
								<code>
									<font color="#000000">
								
										<font color="#FF8000">/* function to process a coupon_code entered by a user */</font>
										<font color="#007700"><br />function</font>
										<font color="#0000BB">process_coupon_code</font><font
										color="#007700">( </font>
										<font color="#0000BB">$d </font>
										<font color="#007700">) {</font>
									</font>
								</code><!-- php buffer end -->
							</code>
						</div>
					</div>
					<div>&nbsp; &nbsp; &nbsp; Enter the following code:</div>
					<div class="codeparent">
						<div class="codephp">PHP Code</div>
						<div class="code">
							<code style="white-space:nowrap">
								<code>
									<font color="#000000">
										<font
										color="#007700">return require_once (</font><font
										color="#0000BB">JPATH_ADMINISTRATOR</font><font
										color="#007700">.</font><font
										color="#0000BB">DS</font><font
										color="#007700">.</font><font
										color="#DD0000">"components"</font><font
										color="#007700">.</font><font
										color="#0000BB">DS</font><font
										color="#007700">.</font><font 
										color="#DD0000">"com_awocoupon"</font><font
										color="#007700">.</font><font
										color="#0000BB">DS</font><font
										color="#007700">.</font><font
										color="#DD0000">"assets"</font><font 
										color="#007700">.</font><font
										color="#0000BB">DS</font><font
										color="#007700">.</font><font
										color="#DD0000">"virtuemart"</font><font
										color="#007700">.</font><font
										color="#0000BB">DS</font><font
										color="#007700">.</font><font
										color="#DD0000">"ps_coupon_process.php"</font><font
										color="#007700">);</font>
						
									</font>
								</code>
							</code>
						</div>
					</div>
				3. &nbsp; Right after these lines (around line 135):<br/>
					<div class="codeparent">
						<div class="codephp">PHP Code:</div>
						<div class="code">
							<code style="white-space:nowrap">
								<code>
									<font color="#000000">
								
										<font color="#FF8000">/* function to remove coupon coupon_code from the database */</font>
										<font color="#007700"><br />function</font>
										<font color="#0000BB">remove_coupon_code</font><font
										color="#007700">( </font>
										<font color="#0000BB">&$d </font>
										<font color="#007700">) {</font>
									</font>
								</code><!-- php buffer end -->
							</code>
						</div>
					</div>
					<div>&nbsp; &nbsp; &nbsp; Enter the following code:</div>
					<div class="codeparent">
						<div class="codephp">PHP Code:</div>
						<div class="code">
							<code style="white-space:nowrap">
								<code>
									<font color="#000000">
										<font
										color="#007700">return require_once (</font><font
										color="#0000BB">JPATH_ADMINISTRATOR</font><font
										color="#007700">.</font><font
										color="#0000BB">DS</font><font
										color="#007700">.</font><font
										color="#DD0000">"components"</font><font
										color="#007700">.</font><font
										color="#0000BB">DS</font><font
										color="#007700">.</font><font 
										color="#DD0000">"com_awocoupon"</font><font
										color="#007700">.</font><font
										color="#0000BB">DS</font><font
										color="#007700">.</font><font
										color="#DD0000">"assets"</font><font 
										color="#007700">.</font><font
										color="#0000BB">DS</font><font
										color="#007700">.</font><font
										color="#DD0000">"virtuemart"</font><font
										color="#007700">.</font><font
										color="#0000BB">DS</font><font
										color="#007700">.</font><font
										color="#DD0000">"ps_coupon_remove.php"</font><font
										color="#007700">);</font>
						
									</font>
								</code>
							</code>
						</div>
					</div>
			</div>
			';
		}

		//import Virtuemart Coupons	
		if(!defined('VM_TABLEPREFIX')) require_once JPATH_ADMINISTRATOR.'/components/com_virtuemart/virtuemart.cfg.php';
		$db = & JFactory::getDBO();
		
		$sql = 'INSERT INTO #__awocoupon (coupon_code,num_of_uses,coupon_value_type,coupon_value,discount_type,function_type)
					SELECT coupon_code,IF(coupon_type="gift",1,0),percent_or_total,coupon_value,"overall",IF(coupon_type="gift","giftcert","coupon")
					  FROM #__'.VM_TABLEPREFIX.'_coupons';
		$db->setQuery($sql);
		if(!$db->query()) echo "<div><b>Step 3: <font color=red>Import of Virtuemart coupons Applied Unsuccessfully</font></b></div>";
		else echo "<div><b>Step 3: <font color=green>Import of Virtuemart coupons Applied Successfully</font></b></div>";	

	}
}
