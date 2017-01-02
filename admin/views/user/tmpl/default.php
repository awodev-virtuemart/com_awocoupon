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

<script language="javascript" type="text/javascript">
<!--
function clearbox(val) {
	var form = document.adminForm;
	if(val == 1) form.elements['userlist[]'].selectedIndex = -1;
	else if(val == 2) form.elements['productlist[]'].selectedIndex = -1;
}
//-->
</script>


<fieldset>
	<div style="float: right">
		<table class="toolbar"><tr>
			<td class="button" id="toolbar-save">
				<a href="#" onclick="javascript: submitbutton('saveuser')" class="toolbar">
				<span class="icon-32-save" title="<?php echo JText::_('SAVE'); ?>"></span><?php echo JText::_('SAVE'); ?></a>
			</td>
			<td class="divider"></td>
			<td class="button" id="toolbar-cancel">
				<a href="#" onclick="javascript: submitbutton('canceluser');" class="toolbar">
				<span class="icon-32-cancel" title="<?php echo JText::_('CANCEL'); ?>"></span><?php echo JText::_('CANCEL'); ?></a>
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


<form action="index.php" method="post" name="adminForm">

	<fieldset class="adminform" style="width:93%;height:300px;">
	<legend><?php echo JText::_('USERS'); ?></legend>
		<select name="userlist[]" class="inputbox" multiple size="7" style="width:100%; height:90%;">
		<?php foreach($this->userlist as $tmp) : ?>
			<option value="<?php echo $tmp->user_id; ?>">(<?php echo $tmp->user_id; ?>) <?php echo $tmp->last_name; ?> <?php echo $tmp->first_name; ?></option>
		<?php endforeach; ?>
		</select>
		<table cellpadding="0" cellspacing="0" width="100%"><tr>
			<td><i style="color:#777777;"><?php echo JText::_('CTRL SHIFT KEY'); ?></i></td>
			<td align="right"><input type="button" onclick="clearbox(1)" value="<?php echo JText::_('CLEAR'); ?>" /></td>
		</tr></table>
	</fieldset>
	</table>

	<input type="hidden" name="option" value="com_awocoupon" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="users" />
	<?php echo JHTML::_( 'form.token' ); ?>

</form>
