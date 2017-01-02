<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access'); 
//window.top.setTimeout('window.parent.document.getElementById(\'sbox-window\').close()', 700);
?>
<script>
window.parent.document.getElementById('pr<?php echo $this->row->id; ?>').innerHTML = '<?php echo empty($this->row->products) ? JText::_( 'ALL' ) : count($this->row->products); ?>';
</script>

<fieldset>
	<div style="float: right">
		<table class="toolbar"><tr>
			<td class="button" id="toolbar-new">
				<a href="#" onclick="javascript: submitbutton('addproduct')" class="toolbar">
				<span class="icon-32-new" title="<?php echo JText::_('NEW'); ?>"></span><?php echo JText::_('NEW'); ?></a>
			</td>
			<td class="divider"></td>
			<td class="button" id="toolbar-delete">
				<a href="#" onclick="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText::_('PLEASE MAKE A SELECTION FROM THE LIST TO DELETE'); ?>');}else{  if(confirm('<?php echo JText::_('ARE YOU SURE YOU WANT TO DELETE THE PRODUCTS'); ?>')){submitbutton('removeproduct');}}" class="toolbar">
				<span class="icon-32-delete" title="<?php echo JText::_('DELETE'); ?>"></span><?php echo JText::_('DELETE'); ?></a>
			</td>
			<td class="divider"></td>
			<td class="button" id="toolbar-cancel">
				<a href="#" onclick="javascript: window.parent.document.getElementById('sbox-window').close();" class="toolbar">
				<span class="icon-32-cancel" title="<?php echo JText::_('CLOSE'); ?>"></span><?php echo JText::_('CLOSE'); ?></a>
			</td>
			<td class="spacer"></td>
		</tr></table>
	</div>
	<div class="" >
		<?php
		if ( $this->row->published == 1 ) {
			$img = com_awocoupon_ASSETS.'/images/published.png';
			$alt = JText::_( 'PUBLISHED' );
		} else {
			$img = com_awocoupon_ASSETS.'/images/unpublished.png';
			$alt = JText::_( 'UNPUBLISHED' );
		}			
		$coupon_value_type = JText::_( $this->row->coupon_value_type=='percent' ? 'PERCENTAGE' : 'TOTAL' );
		$discount_type = JText::_( $this->row->discount_type=='specific' ? 'SPECIFIC' : 'OVERALL' );
		$function_type = JText::_( 'COUPON' );
		
		$num_of_uses_type = '';
		if($this->row->num_of_uses_type=='total') $num_of_uses_type = JText::_( 'TOTAL' );
		elseif($this->row->num_of_uses_type=='per_user') $num_of_uses_type = JText::_( 'PER CUSTOMER' );
		$num_of_uses = empty($this->row->num_of_uses) ? JText::_( 'UNLIMITED' ) : $this->row->num_of_uses.' '.$num_of_uses_type;
		?>
		<table class="admintable">
		<tr><td class="key"><label><?php echo JText::_( 'COUPON CODE' ); ?></label></td>
			<td><?php echo $this->row->coupon_code; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'PUBLISHED' ); ?></label></td>
			<td ><?php echo '<img src="'.$img.'" width="16" height="16" border="0" alt="'.$alt.'" title="'.$alt.'" />'; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'FUNCTION TYPE' ); ?></label></td>
			<td><?php echo $function_type; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'NUMBER OF USES' ); ?></label></td>
			<td><?php echo $num_of_uses; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'PERCENT OR TOTAL' ); ?></label></td>
			<td><?php echo $coupon_value_type; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'DISCOUNT TYPE' ); ?></label></td>
			<td><?php echo $discount_type; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'VALUE' ); ?></label></td>
			<td><?php echo $this->row->coupon_value; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'MINIMUM VALUE' ); ?></label></td>
			<td><?php echo $this->row->min_value; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'EXPIRATION' ); ?></label></td>
			<td><?php echo $this->row->expiration; ?></td>
		</tr>
		</table>
		
	</div>
</fieldset>


<fieldset><legend><?php echo JText::_( 'PRODUCTS' );?></legend>
<form action="index.php" method="post" name="adminForm">

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
			<th width="5"><?php echo JText::_( 'NUM' ); ?></th>
			<th width="5"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->row->products ); ?>);" /></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'ID', 'pv.product_id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'PRODUCT NAME', 'pv.product_name', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		</tr>
	</thead>
	<tfoot><tr><td colspan="10"><?php echo $this->pageNav->getListFooter(); ?></td></tr></tfoot>

	<tbody>
		<?php
		
		foreach ($this->row->products as $i=>$row) :
		?>
		<tr class="row<?php echo ($i%2); ?>">
			<td><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td width="7"><?php echo JHTML::_('grid.id', $i,$row->product_id ); ?></td>
			<td align="center"><?php echo $row->product_id; ?></td>
			<td align="center"><?php echo $row->product_name.' ('.$row->product_sku.')'; ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>

	</table>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_awocoupon" />
	<input type="hidden" name="view" value="products" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</fieldset>