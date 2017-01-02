<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

function write_to_ps_coupon() {
	$file = JPATH_SITE.'/administrator/components/com_virtuemart/classes/ps_coupon.php';

	if (!file_exists($file) || !is_writable($file)) return FALSE;

	// get and replace content
	$content = file_get_contents($file);
	$patterns = array(
		'/return\s+require_once\s*\(\s*JPATH_ADMINISTRATOR\s*.\s*DS\s*.\s*"components"\s*.\s*DS\s*.\s*"com_awocoupon"\s*.\s*DS\s*.\s*"assets"\s*.\s*DS\s*.\s*"virtuemart"\s*.\s*DS\s*.\s*"ps_coupon_process.php"\s*\)\s*;[\n\r]*/i',
		'/return\s+require_once\s*\(\s*JPATH_ADMINISTRATOR\s*.\s*DS\s*.\s*"components"\s*.\s*DS\s*.\s*"com_awocoupon"\s*.\s*DS\s*.\s*"assets"\s*.\s*DS\s*.\s*"virtuemart"\s*.\s*DS\s*.\s*"ps_coupon_remove.php"\s*\)\s*;[\n\r]*/i',
		);
	$count = 0;
	$new_content = preg_replace($patterns, '', $content, -1, $count);
	//echo '<textarea cols="120" rows="35">'.$new_content.'</textarea>';
	if($count != 2) return FALSE;

	if (!@$handle = fopen($file, 'w')) return FALSE;

	// Write $somecontent to our opened file.
	if (fwrite($handle, $new_content) === FALSE) return FALSE;
	
	fclose($handle);
	return TRUE;

}
function com_uninstall(){
	
	echo '<div><b>Step 1: <font color="green">Successful</font></b></div>';

	if(write_to_ps_coupon()) {
		echo '<div><b>Step 2: <font color="green">Successful</font></b></div>';
	} else {
		echo '
		<div><b>Step 2: <font color="red">Unsuccessful</font></b></div>
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
			We are sorry <b>AwoCoupon for Virtuemart</b> did not meet your needs. 
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
				<div>&nbsp; &nbsp; &nbsp; Remove the following code:</div>
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
				<div>&nbsp; &nbsp; &nbsp; Remove the following code:</div>
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
}
