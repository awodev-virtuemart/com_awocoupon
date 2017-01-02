<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

?>
<table cellpadding="4" cellspacing="0" border="0" width="100%">
	<tr><td width="100%"><img src="<?php echo com_awocoupon_ASSETS . '/images/logo.png'; ?>" style="margin-left:10px;" /></td></tr>
	<tr><td>
			<blockquote>
				<p><?php echo JText::_('COM_AWOCOUPON_AT_CREATE'); ?></p>
				<p><?php echo JText::_('COM_AWOCOUPON_AT_VISIT'); ?></p>
				<p>&nbsp;</p>
			</blockquote>
		</td>
	</tr>
	<tr>
		<td>
			<div style="font-weight: 700;">
				<?php echo JText::sprintf( 'COM_AWOCOUPON_DH_CURRENT_VERSION', $this->version ); ?>
			</div>
		</td>
	</tr>
</table>