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

	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td width="55%" valign="top">
				<div id="cpanel">
					<?php echo $this->addIcon('icon-48-new','coupon', 		JText::_('COM_AWOCOUPON_DH_COUPON_NEW'));?>
					<?php echo $this->addIcon('coupons','coupons', 			JText::_('COM_AWOCOUPON_CP_COUPONS'));?>
					<?php echo $this->addIcon('icon-48-installation','installation', 	JText::_('COM_AWOCOUPON_FI_INSTALLATION_CHECK'),false);?>
					<hr style="clear:both;margin-right:7px;" />
					<div>
						<div style="color:red;font-weight:bold;text-align:center;"><?php echo JText::_('COM_AWOCOUPON_AT_PROONLY') ?></div>
						<?php echo $this->addIcon('icon-48-giftcert','dashboard',		JText::_('COM_AWOCOUPON_GC_GIFTCERTS'),false);?>
						<?php echo $this->addIcon('icon-48-profile','dashboard',			JText::_('COM_AWOCOUPON_PF_PROFILES'));?>
						<?php echo $this->addIcon('icon-48-history','dashboard',			JText::_('COM_AWOCOUPON_CP_HISTORY_USES'),false);?>
						<?php echo $this->addIcon('icon-48-import','dashboard', 			JText::_('COM_AWOCOUPON_IMP_IMPORT'),false);?>
						<?php echo $this->addIcon('icon-48-report','dashboard', 			JText::_('COM_AWOCOUPON_RPT_REPORTS'),false);?>
						<?php echo $this->addIcon('icon-48-config','dashboard', 			JText::_('COM_AWOCOUPON_CFG_CONFIGURATION'),false);?>
					</div>
				</div>
				
			</td>
			
			
			
			<td width="45%" valign="top">
			
			
			
			
		<table class="adminlist" border="0">
		<tbody>
		<tr>
		  	<td align="center" style="border: solid 1px #ccc;">
			<div style="font-weight:bold;font-size:12px;color:#000000;"><?php echo JText::_('COM_AWOCOUPON_JOOMLA_REVIEW'); ?></div>
			</td>
		</tr>
		</tbody>
		</table>




		
				<?php
				if(version_compare( JVERSION, '1.6.0', 'ge' )) {
					echo JHtml::_('sliders.start', 'genstat-pane');
					echo JHtml::_('sliders.panel', JText::_( 'COM_AWOCOUPON_DH_GENERAL_STATISTICS' ), 'unapproved');
				}
				else {
					$this->pane   	= JPane::getInstance('sliders');
					echo $this->pane->startPane( 'genstat-pane' );
					echo $this->pane->startPanel( JText::_( 'COM_AWOCOUPON_DH_GENERAL_STATISTICS' ), 'unapproved' );
				}
				?>
				<div id="dash_generalstats" class="postbox " >
					<div class="inside">
						<div class="table">
							<table>
							<tr class="first">
								<td class="first b"><a href="index.php?option=<?php echo AWOCOUPON_OPTION ?>&view=coupons&filter_state=&filter_coupon_value_type=&filter_discount_type=&filter_function_type="><?php echo $this->genstats['total']; ?></a></td>
								<td class="t"><?php echo JText::_('COM_AWOCOUPON_DH_COUPON_TOTAL');?></td>
							</tr>
							<tr><td class="first b"><a href="index.php?option=<?php echo AWOCOUPON_OPTION ?>&view=coupons&filter_state=1&filter_coupon_value_type=&filter_discount_type=&filter_function_type="><?php echo $this->genstats['active']; ?></a></td>
								<td class=" t approved"><?php echo JText::_('COM_AWOCOUPON_DH_COUPON_ACTIVE');?></td>
							</tr>
							<tr><td class="first b"><a href="index.php?option=<?php echo AWOCOUPON_OPTION ?>&view=coupons&filter_state=-1&filter_coupon_value_type=&filter_discount_type=&filter_function_type="><?php echo $this->genstats['inactive']; ?></a></td>
								<td class=" t inactive"><?php echo JText::_('COM_AWOCOUPON_DH_COUPON_INACTIVE');?></td>
							</tr>
							</table>
						</div>
					</div>
				</div>
				<?php
				if(version_compare( JVERSION, '1.6.0', 'ge' )) {
					echo JHtml::_('sliders.panel', JText::_( 'COM_AWOCOUPON_DH_COUPON_RECENT' ), 'mostpop-pane' );
				} else {
					echo $this->pane->endPanel();
					echo $this->pane->startPanel( JText::_( 'COM_AWOCOUPON_DH_COUPON_RECENT' ), 'mostpop-pane' );
				}
				?>
				<table class="adminlist">
					<thead>
						<tr>
							<td class="title"><strong><?php echo JText::_( 'COM_AWOCOUPON_CP_COUPON_CODE' ); ?></strong></td>
							<td class="title"><strong><?php echo JText::_( 'COM_AWOCOUPON_CP_VALUE_TYPE' ); ?></strong></td>
							<td class="title"><strong><?php echo JText::_( 'COM_AWOCOUPON_CP_VALUE' ); ?></strong></td>
						</tr>
					</thead>
					<tbody>
						<?php
						$k = 0;
						for ($i=0, $n=count($this->lastentered); $i < $n; $i++) {
						$row = $this->lastentered[$i];
						$link 		= 'index.php?option='.AWOCOUPON_OPTION.'&amp;controller=coupons&amp;task=editcoupon&amp;cid[]='. $row->id;
						$coupon_value_type = $this->def_lists['coupon_value_type'][$row->coupon_value_type];
					?>
						<tr class="row<?php echo $k; ?>">
							<td width="65%">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_AWOCOUPON_CP_COUPON' ); ?>::<?php echo $row->coupon_code; ?>">
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
				if(version_compare( JVERSION, '1.6.0', 'ge' )) {
					echo JHtml::_('sliders.end');
				}
				else {
					echo $this->pane->endPanel(); 
				}
				
				?>
		
		
		
		<table class="adminlist" border="0">
		<thead><tr><th colspan="2"><?php echo JText::_('AwoCoupon Pro'); ?></th></tr></thead>
		<tbody>
		<tr>
		  	<td align="center">
				<a href="http://awodev.com/products/joomla/awocoupon" target="_blank"><img src="<?php echo com_awocoupon_ASSETS.'/images/awoprologo.png'; ?>" alt="awocouponpro"></a>
				<div valign="middle"><?php echo JText::_('COM_AWOCOUPON_AT_PRONOTE'); ?></div>
			</td>
		</tr>
		</tbody>
		</table>

		
			</td>
		</tr>
	</table>