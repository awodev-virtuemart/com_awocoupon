<?php
/*
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 */
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');
global $mainframe;
?>

	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td width="55%" valign="top">
				<div id="cpanel">
					<?php echo $this->addIcon('icon-48-new','coupon', 		JText::_('NEW COUPON'));?>
					<?php echo $this->addIcon('coupons','coupons', 			JText::_('COUPONS'));?>
					<?php //echo $this->addIcon('config','settings', 			JText::_('SETTINGS'));?>					
					<hr style="clear:both;margin-right:7px;" />
					<div>
						<div style="color:red;font-weight:bold;text-align:center;"><?php echo JText::_('PRO VERSION ONLY') ?></div>
						<?php echo $this->addIcon('icon-48-giftcert','',		JText::_('GIFT CERTIFICATES'),false);?>
						<?php echo $this->addIcon('icon-48-history','',		JText::_('HISTORY OF USES'),false);?>
						<?php echo $this->addIcon('icon-48-import','', 		JText::_('IMPORT'),false);?>
						<?php echo $this->addIcon('icon-48-report','', 		JText::_('REPORTS'),false);?>
						<?php echo $this->addIcon('icon-48-config','', 		JText::_('CONFIGURATION'),false);?>
						<?php echo $this->addIcon('icon-48-installation','', 	JText::_('INSTALLATION CHECK'),false);?>
					</div>
				</div>
			</td>
			<td width="45%" valign="top">
				<?php
				echo $this->pane->startPane( 'genstat-pane' );
				echo $this->pane->startPanel( JText::_( 'GENERAL STATISTICS' ), 'unapproved' );
				?>
				<div id="dash_generalstats" class="postbox " >
					<div class="inside">
						<div class="table">
							<table>
							<tr class="first">
								<td class="first b"><a href="index.php?option=com_awocoupon&view=coupons&filter_state=&filter_coupon_value_type=&filter_discount_type=&filter_function_type="><?php echo $this->genstats['total']; ?></a></td>
								<td class="t"><?php echo JText::_('TOTAL COUPONS');?></td>
							</tr>
							<tr><td class="first b"><a href="index.php?option=com_awocoupon&view=coupons&filter_state=1&filter_coupon_value_type=&filter_discount_type=&filter_function_type="><?php echo $this->genstats['active']; ?></a></td>
								<td class=" t approved"><?php echo JText::_('ACTIVE COUPONS');?></td>
							</tr>
							<tr><td class="first b"><a href="index.php?option=com_awocoupon&view=coupons&filter_state=-1&filter_coupon_value_type=&filter_discount_type=&filter_function_type="><?php echo $this->genstats['inactive']; ?></a></td>
								<td class=" t inactive"><?php echo JText::_('INACTIVE COUPONS');?></td>
							</tr>
							</table>
						</div>
					</div>
				</div>
				<?php
				echo $this->pane->endPanel();
				echo $this->pane->startPanel( JText::_( 'RECENT COUPONS' ), 'mostpop-pane' );
				?>
				<table class="adminlist">
					<thead>
						<tr>
							<td class="title"><strong><?php echo JText::_( 'COUPON CODE' ); ?></strong></td>
							<td class="title"><strong><?php echo JText::_( 'VALUE TYPE' ); ?></strong></td>
							<td class="title"><strong><?php echo JText::_( 'VALUE' ); ?></strong></td>
						</tr>
					</thead>
					<tbody>
						<?php
						$k = 0;
						for ($i=0, $n=count($this->lastentered); $i < $n; $i++) {
						$row = $this->lastentered[$i];
						$link 		= 'index.php?option=com_awocoupon&amp;controller=coupons&amp;task=editcoupon&amp;cid[]='. $row->id;
						$coupon_value_type = JText::_( $row->coupon_value_type=='percent' ? 'PERCENTAGE' : 'TOTAL' );
					?>
						<tr class="row<?php echo $k; ?>">
							<td width="65%">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'EDIT COUPON' ); ?>::<?php echo $row->coupon_code; ?>">
									<a href="<?php echo $link; ?>">
										<?php echo htmlspecialchars($row->coupon_code, ENT_QUOTES, 'UTF-8'); ?>
									</a>
								</span>
							</td>
							<td align="center"><strong><?php echo $coupon_value_type; ?></strong></td>
							<td width="5%" align="center"><strong><?php echo $row->coupon_value; ?></strong></td>
						</tr>
						<?php $k = 1 - $k; } ?>
					</tbody>
				</table>
				<?php
				echo $this->pane->endPanel(); ?>
		
		<table class="adminlist" border="0">
		<thead><tr><th colspan="2"><?php echo JText::_('AWOCOUPONPRO'); ?></th></tr></thead>
		<tbody>
		<tr>
		  	<td align="center">
				<a href="http://awodev.com/product/awocoupon-pro" target="_blank"><img src="<?php echo com_awocoupon_ASSETS.'/images/awoprologo.png'; ?>" alt="awocouponpro"></a>
				<div valign="middle"><?php echo JText::_('PRONOTE'); ?></div>
			</td>
		</tr>
		</tbody>
		</table>
			</td>
		</tr>
	</table>