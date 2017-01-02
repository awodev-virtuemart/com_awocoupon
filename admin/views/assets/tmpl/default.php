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

<fieldset>
	<div class="" >
		<?php
		if ( $this->row->published == 1 ) {
			$img = com_awocoupon_ASSETS.'/images/published.png';
			$alt = JText::_( 'PUBLISHED' );
		} else {
			$img = com_awocoupon_ASSETS.'/images/unpublished.png';
			$alt = JText::_( 'UNPUBLISHED' );
		}			
		$function_type = JText::_( 'COM_AWOCOUPON_CP_COUPON' );
		$coupon_value_type = $this->def_lists['coupon_value_type'][$this->row->coupon_value_type];
		$discount_type = $this->def_lists['discount_type'][$this->row->discount_type];
		
		$num_of_uses_type = '';
		if($this->row->num_of_uses_type=='total') $num_of_uses_type = JText::_( 'COM_AWOCOUPON_GBL_TOTAL' );
		elseif($this->row->num_of_uses_type=='per_user') $num_of_uses_type = JText::_( 'COM_AWOCOUPON_CP_PER_CUSTOMER' );
		$num_of_uses = empty($this->row->num_of_uses) ? JText::_( 'UNLIMITED' ) : $this->row->num_of_uses.' '.$num_of_uses_type;
		?>
		<table class="admintable">
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_COUPON_CODE' ); ?></label></td>
			<td><?php echo $this->row->coupon_code; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' ); ?></label></td>
			<td ><?php echo '<img src="'.$img.'" width="16" height="16" border="0" alt="'.$alt.'" title="'.$alt.'" />'; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_FUNCTION_TYPE' ); ?></label></td>
			<td><?php echo $function_type; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_NUMBER_USES' ); ?></label></td>
			<td><?php echo $num_of_uses; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_PERCENT_AMOUNT' ); ?></label></td>
			<td><?php echo $coupon_value_type; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_DISCOUNT_TYPE' ); ?></label></td>
			<td><?php echo $discount_type; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_VALUE' ); ?></label></td>
			<td><?php echo $this->row->coupon_value; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_VALUE_MIN' ); ?></label></td>
			<td><?php echo $this->row->min_value; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_DATE_START' ); ?></label></td>
			<td><?php echo $this->row->startdate; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_EXPIRATION' ); ?></label></td>
			<td><?php echo $this->row->expiration; ?></td>
		</tr>
		</table>
		
	</div>
</fieldset>


<fieldset><legend><?php echo JText::_( 'COM_AWOCOUPON_CP_ASSET' );?></legend>
<form action="index.php" method="post" id="adminForm" name="adminForm">

	<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="5"><?php echo JText::_( 'COM_AWOCOUPON_GBL_NUM' ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_GBL_ID', 'asset_id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', $this->row->function_type2=='product' ? 'COM_AWOCOUPON_CP_PRODUCT' : 'COM_AWOCOUPON_CP_CATEGORY', 'asset_name', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php
		
		foreach ($this->row->assets as $i=>$row) :
		?>
		<tr class="row<?php echo ($i%2); ?>">
			<td><?php echo ($i+1); ?></td>
			<td align="center"><?php echo $row->asset_id; ?></td>
			<td align="center"><?php echo $row->asset_name; ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>

	</table>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="<?php echo AWOCOUPON_OPTION; ?>" />
	<input type="hidden" name="view" value="assets" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</fieldset>