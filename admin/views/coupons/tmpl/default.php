<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" id="adminForm" name="adminForm">

	<table class="adminform">
		<tr>
			<td width="100%">
			  	<?php echo JText::_( 'COM_AWOCOUPON_GBL_SEARCH' ); ?>
				<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onChange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'COM_AWOCOUPON_GBL_GO' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_AWOCOUPON_GBL_RESET' ); ?></button>
			</td>
			<td nowrap="nowrap"></td>
			<td nowrap="nowrap">
				<?php echo $this->lists['coupon_value_type']; ?>
				<?php echo $this->lists['discount_type']; ?>
				<?php echo $this->lists['published']; ?>
			</td>
		</tr>
	</table>

	<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="5"><?php echo JText::_( 'COM_AWOCOUPON_GBL_NUM' ); ?></th>
			<th width="5"><input type="checkbox" name="toggle" value="" onClick="<?php echo version_compare( JVERSION, '1.6.0', 'ge' ) ? 'Joomla.checkAll(this)' : 'checkAll('.count( $this->rows ).')'; ?>;" /></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_COUPON_CODE', 'c.coupon_code', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_FUNCTION_TYPE', 'c.function_type', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_VALUE_TYPE', 'c.coupon_value_type', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_VALUE', 'c.coupon_value', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_NUMBER_USES', 'c.num_of_uses', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title" width="1%"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_VALUE_MIN', 'c.min_value', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_DISCOUNT_TYPE', 'c.discount_type', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_AWOCOUPON_CP_CUSTOMERS' ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_AWOCOUPON_CP_ASSET' ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_DATE_START', 'c.startdate', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_EXPIRATION', 'c.expiration', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_GBL_ID', 'c.id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		</tr>
	</thead>
	<tfoot><tr><td colspan="17"><?php echo $this->pageNav->getListFooter(); ?></td></tr></tfoot>

	<tbody>
		<?php
		foreach ($this->rows as $i=>$row) :
			if ( $row->published == 1 ) {
				$img = com_awocoupon_ASSETS.'/images/published.png';
				$alt = JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' );
				$imgjs = 'onclick="listItemTask(\'cb'.$i.'\',\'unpublishcoupon\')" style="cursor:pointer;" ';
			} else{
				$img = com_awocoupon_ASSETS.'/images/unpublished.png';
				$alt = JText::_( 'COM_AWOCOUPON_CP_UNPUBLISHED' );
				$imgjs = 'onclick="listItemTask(\'cb'.$i.'\',\'publishcoupon\')" style="cursor:pointer;" ';
			}			
			

			//$function_type = $this->def_lists['function_type'][$row->function_type];
			$coupon_value_type = $this->def_lists['coupon_value_type'][$row->coupon_value_type];
			$coupon_value = !empty($row->coupon_value) ? number_format($row->coupon_value,2): $row->coupon_value_def;
			$discount_type = $this->def_lists['discount_type'][$row->discount_type];
			$num_of_uses_type = '';
			if($row->num_of_uses_type=='total') $num_of_uses_type = JText::_( 'COM_AWOCOUPON_GBL_TOTAL' );
			elseif($row->num_of_uses_type=='per_user') $num_of_uses_type = JText::_( 'COM_AWOCOUPON_CP_PER_CUSTOMER' );
			$num_of_uses = empty($row->num_of_uses) ? JText::_( 'COM_AWOCOUPON_GBL_UNLIMITED' ) : $row->num_of_uses.' '.$num_of_uses_type;
			$usercount = empty($row->usercount) ? JText::_( 'COM_AWOCOUPON_GBL_ALL' ) : $row->usercount;
			
		?>
		<tr class="row<?php echo ($i%2); ?>">
			<td><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td width="7"><?php echo JHTML::_('grid.id', $i,$row->id ); ?></td>
			<td align="left">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_AWOCOUPON_CP_COUPON' ); ?>::<?php echo $row->coupon_code; ?>">
					<a href="index.php?option=<?php echo AWOCOUPON_OPTION; ?>&amp;controller=coupons&amp;task=editcoupon&amp;cid[]=<?php echo $row->id; ?>"><?php echo $row->coupon_code; ?></a>
				</span>
			</td>
			<td align="center"><?php echo JText::_( 'COM_AWOCOUPON_CP_COUPON' ); ?>&nbsp;</td>
			<td align="center"><?php echo $coupon_value_type; ?>&nbsp;</td>
			<td align="center"><?php echo $coupon_value; ?>&nbsp;</td>
			<td align="center"><?php echo $num_of_uses; ?>&nbsp;</td>
			<td align="center"><?php echo $row->min_value; ?>&nbsp;</td>
			<td align="center"><?php echo $discount_type; ?>&nbsp;</td>
			<td align="center" nowrap>
				<?php if(!empty($row->usercount)) { ?>
				<a class="modal" href="index.php?option=<?php echo AWOCOUPON_OPTION; ?>&amp;view=users&id=<?php echo $row->id; ?>" rel="{handler: 'iframe', size: {x: 580, y: 550}}">
					<span><?php echo JText::_('COM_AWOCOUPON_CP_CUSTOMERS'); ?> (<?php echo $row->usercount; ?>)</span>
				</a>
				<?php } else echo JText::_( 'COM_AWOCOUPON_GBL_ALL' ); ?>&nbsp;
			</td>
			<td align="center" nowrap>
				<?php if(!empty($row->assetcount)) { ?>
				<a class="modal" href="index.php?option=<?php echo AWOCOUPON_OPTION; ?>&amp;view=assets&id=<?php echo $row->id; ?>" rel="{handler: 'iframe', size: {x: 580, y: 550}}">
					<span><?php echo $discount_type = $this->def_lists['function_type2'][$row->function_type2];; ?> (<?php echo $row->assetcount; ?>)</span>
				</a>
				<?php } else echo JText::_( 'COM_AWOCOUPON_GBL_ALL' ); ?>&nbsp;
			</td>
			<td align="center"><?php echo str_replace(' ','<br />',$row->startdate); ?>&nbsp;</td>
			<td align="center"><?php echo str_replace(' ','<br />',$row->expiration); ?>&nbsp;</td>
			<td align="center"><?php echo '<img src="'.$img.'" width="16" height="16" class="hand" border="0" alt="'.$alt.'" title="'.$alt.'" '.$imgjs.'/>'; ?></td>
			<td align="center"><?php echo $row->id; ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>

	</table>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="<?php echo AWOCOUPON_OPTION; ?>" />
	<input type="hidden" name="view" value="coupons" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>