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
<div class="row">
	<div class="col-12">
		<ul class="nav nav-tabs">
			<li class="nav-item"><a class="nav-link" href="<?php echo JRoute::_( 'index.php?option=com_awocoupon&view=dashboard' ); ?>" ><?php echo JText::_( 'COM_AWOCOUPON_DH_DASHBOARD' ); ?></a></li>
			<li class="nav-item"><a class="nav-link" href="<?php echo JRoute::_( 'index.php?option=com_awocoupon&view=coupon' ); ?>" ><?php echo JText::_( 'COM_AWOCOUPON_CP_COUPONS' ); ?></a></li>
			<li class="nav-item"><a class="nav-link" href="<?php echo JRoute::_( 'index.php?option=com_awocoupon&view=installation' ); ?>" ><?php echo JText::_( 'COM_AWOCOUPON_FI_INSTALLATION_CHECK' ); ?></a></li>
			<li class="nav-item"><a class="nav-link active" href="<?php echo JRoute::_( 'index.php?option=com_awocoupon&view=about' ); ?>" ><?php echo JText::_( 'COM_AWOCOUPON_AT_ABOUT' ); ?></a></li>
		</ul>
		<br />

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

	</div>
</div>