<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access'); 

if ( empty( $this->coupon->id ) ) {
	return;
}
?>
<fieldset>
	<div class="" >
		<table class="admintable">
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_COUPON_CODE' ); ?></label></td>
			<td><?php echo $this->coupon->coupon_code; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' ); ?></label></td>
			<td ><img src="<?php echo com_awocoupon_ASSETS; ?>/images/<?php echo ( $this->coupon->published ? 'published.png' : 'unpublished.png' ); ?>" width="16" height="16" border="0" /></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_FUNCTION_TYPE' ); ?></label></td>
			<td><?php echo JText::_( 'COM_AWOCOUPON_CP_COUPON' ); ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_NUMBER_USES' ); ?></label></td>
			<td><?php echo ( empty( $this->coupon->num_of_uses ) ? JText::_( 'UNLIMITED' ) : $this->coupon->num_of_uses . ' ' . $this->def_lists['num_of_uses_type'][ $this->coupon->num_of_uses_type ] ); ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_PERCENT_AMOUNT' ); ?></label></td>
			<td><?php echo $this->def_lists['coupon_value_type'][ $this->coupon->coupon_value_type ]; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_DISCOUNT_TYPE' ); ?></label></td>
			<td><?php echo $this->def_lists['discount_type'][ $this->coupon->discount_type ]; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_VALUE' ); ?></label></td>
			<td><?php echo $this->coupon->coupon_value; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_VALUE_MIN' ); ?></label></td>
			<td><?php echo $this->coupon->min_value; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_DATE_START' ); ?></label></td>
			<td><?php echo $this->coupon->startdate; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_EXPIRATION' ); ?></label></td>
			<td><?php echo $this->coupon->expiration; ?></td>
		</tr>
		</table>
		
	</div>
</fieldset>

<fieldset><legend><?php echo JText::_( 'COM_AWOCOUPON_CP_CUSTOMERS' );?></legend>
<form action="<?php echo JRoute::_( 'index.php?option=com_awocoupon&view=coupon&layout=users&tmpl=component' ); ?>" method="post" name="adminForm" id="adminForm">

	<table class="adminform">
		<tr>
			<td width="100%">
			</td>
			<td nowrap="nowrap"></td>
		</tr>
	</table>

	<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_GBL_ID', 'uv.virtuemart_user_id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_GBL_LAST_NAME', 'uv.last_name', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_GBL_FIRST_NAME', 'uv.first_name', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		</tr>
	</thead>
	<tfoot><tr><td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>

	<tbody>
		<?php
		
		foreach ($this->items as $i=>$row) :
		?>
		<tr class="row<?php echo ($i%2); ?>">
			<td><?php echo $row->user_id; ?></td>
			<td><?php echo $row->last_name; ?></td>
			<td><?php echo $row->first_name; ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>

	</table>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="<?php echo AWOCOUPON_OPTION; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->coupon->id; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</fieldset>