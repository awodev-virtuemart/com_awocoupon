<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm">

	<table class="adminform">
		<tr>
			<td width="100%">
			  	<?php echo JText::_( 'SEARCH' ); ?>
				<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onChange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'GO' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'RESET' ); ?></button>
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
			<th width="5"><?php echo JText::_( 'NUM' ); ?></th>
			<th width="5"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->rows ); ?>);" /></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COUPON CODE', 'c.coupon_code', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'FUNCTION TYPE', 'c.function_type', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'NUMBER OF USES', 'c.num_of_uses', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'VALUE TYPE', 'c.coupon_value_type', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'VALUE', 'c.coupon_value', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'MINIMUM VALUE', 'c.min_value', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'DISCOUNT TYPE', 'c.discount_type', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'USERS' ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'PRODUCTS' ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'EXPIRATION', 'c.expiration', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'PUBLISHED' ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'c.id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		</tr>
	</thead>
	<tfoot><tr><td colspan="14"><?php echo $this->pageNav->getListFooter(); ?></td></tr></tfoot>

	<tbody>
		<?php
		
		foreach ($this->rows as $i=>$row) :
			if ( $row->published == 1 ) {
				$img = com_awocoupon_ASSETS.'/images/published.png';
				$alt = JText::_( 'PUBLISHED' );
			} else {
				$img = com_awocoupon_ASSETS.'/images/unpublished.png';
				$alt = JText::_( 'UNPUBLISHED' );
			}			
			$coupon_value_type = JText::_( $row->coupon_value_type=='percent' ? 'PERCENTAGE' : 'TOTAL' );
			$discount_type = JText::_( $row->discount_type=='specific' ? 'SPECIFIC' : 'OVERALL' );
			$function_type = JText::_('COUPON');
			$usercount = empty($row->usercount) ? JText::_( 'ALL' ) : $row->usercount;
			$productcount = empty($row->productcount) ? JText::_( 'ALL' ) : $row->productcount;
			
			$num_of_uses_type = '';
			if($row->num_of_uses_type=='total') $num_of_uses_type = JText::_( 'TOTAL' );
			elseif($row->num_of_uses_type=='per_user') $num_of_uses_type = JText::_( 'PER CUSTOMER' );
			$num_of_uses = empty($row->num_of_uses) ? JText::_( 'UNLIMITED' ) : $row->num_of_uses.' '.$num_of_uses_type;

		?>
		<tr class="row<?php echo ($i%2); ?>">
			<td><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td width="7"><?php echo JHTML::_('grid.id', $i,$row->id ); ?></td>
			<td align="left">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'EDIT COUPON' ); ?>::<?php echo $row->coupon_code; ?>">
					<a href="index.php?option=com_awocoupon&amp;controller=coupons&amp;task=editcoupon&amp;cid[]=<?php echo $row->id; ?>"><?php echo $row->coupon_code; ?></a>
				</span>
			</td>
			<td align="center"><?php echo $function_type; ?></td>
			<td align="center"><?php echo $num_of_uses; ?></td>
			<td align="center"><?php echo $coupon_value_type; ?></td>
			<td align="center"><?php echo $row->coupon_value; ?></td>
			<td align="center"><?php echo $row->min_value; ?></td>
			<td align="center"><?php echo $discount_type; ?></td>
			<td align="center" nowrap>
				<span id="ur<?php echo $row->id; ?>"><?php echo $usercount; ?></span>
				[<a class="modal" href="index.php?option=com_awocoupon&amp;view=users&id=<?php echo $row->id; ?>" rel="{handler: 'iframe', size: {x: 580, y: 550}}">
					<span><?php echo JText::_('EDIT'); ?></span>
				</a>]
			</td>
			<td align="center" nowrap>
				<span id="pr<?php echo $row->id; ?>"><?php echo $productcount; ?></span>
				[<a class="modal" href="index.php?option=com_awocoupon&amp;view=products&id=<?php echo $row->id; ?>" rel="{handler: 'iframe', size: {x: 580, y: 550}}">
					<span><?php echo JText::_('EDIT'); ?></span>
				</a>]
			</td>
			<td align="center"><?php echo $row->expiration; ?>&nbsp;</td>
			<td align="center"><?php echo '<img src="'.$img.'" width="16" height="16" border="0" alt="'.$alt.'" title="'.$alt.'" />'; ?></td>
			<td align="center"><?php echo $row->id; ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>

	</table>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_awocoupon" />
	<input type="hidden" name="controller" value="coupons" />
	<input type="hidden" name="view" value="coupons" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>