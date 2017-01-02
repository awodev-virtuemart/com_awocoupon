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
<script language="javascript" type="text/javascript">
<!--
function clearbox(val) {
	var form = document.adminForm;
	if(val == 1) form.elements['userlist[]'].selectedIndex = -1;
	else if(val == 2) form.elements['productlist[]'].selectedIndex = -1;
}
function isUnsignedInteger(s) {
	return (s.toString().search(/^[0-9]+$/) == 0);
}

function submitbutton(pressbutton) {
	var form = document.adminForm;

	if (pressbutton == 'cancelcoupon') {
		submitform( pressbutton );
		return;
	}
	
	var err = '';
	if(trim(form.coupon_code.value)=='') err += '\n<?php echo JText::_('PLEASE ENTER A COUPON CODE'); ?>';
	
	if(form.num_of_uses_type.value!='' && form.num_of_uses_type.value!='total' && form.num_of_uses_type.value!='per_user') err += '\n<?php echo addslashes(JText::_('PLEASE ENTER A VALID NUMBER OF USES TYPE')); ?>';
	if(trim(form.num_of_uses.value)!='' && !isUnsignedInteger(form.num_of_uses.value)) err += '\n<?php echo addslashes(JText::_('PLEASE ENTER A VALID NUMBER OF USES')); ?>';
	if( (form.num_of_uses_type.value!='' && trim(form.num_of_uses.value)=='') || (form.num_of_uses_type.value=='' && trim(form.num_of_uses.value)!='')) err += '\n<?php echo addslashes(JText::_('PLEASE ENTER A VALID NUMBER OF USES')); ?>';
	
	if(trim(form.coupon_value_type.value)=='') err += '\n<?php echo JText::_('PLEASE ENTER VALUE TYPE'); ?>'; 
	if(trim(form.discount_type.value)=='specific' && form.elements['productlist[]'].selectedIndex==-1) err += '\n<?php echo JText::_('PLEASE SELECT AT LEAST ONE PRODUCT FOR DISCOUNT TYPE OF SPECIFIC'); ?>';
	if(trim(form.coupon_value.value)=='' || isNaN(form.coupon_value.value) || form.coupon_value.value<0.01) err += '\n<?php echo JText::_('PLEASE ENTER A VALID VALUE'); ?>'; 
	if(trim(form.min_value.value)!='' && form.min_value.value!=0 && (isNaN(form.min_value.value) || form.min_value.value<0.01)) err += '\n<?php echo JText::_('PLEASE ENTER A VALID MIN VALUE'); ?>'; 
	//if(trim(form.discount_type.value)=='specific' && form.coupon_value_type.value=='total') err += '\n<?php echo JText::_('CANNOT HAVE A DISCOUNT TYPE OF SPECIFIC WITH A VALUE TYPE OF TOTAL'); ?>';
	
	if(err != '') alert(err);
	else {
		var is_submit = true;
		if(trim(form.expiration.value)!='') {
			d = form.expiration.value.substr(0,4)+form.expiration.value.substr(5,2)+form.expiration.value.substr(8,2);
			d = d*1;
			if(d < <?php echo date('Ymd'); ?>) {
				if(!confirm('<?php echo JText::_('EXPIRATION DATE IS IN THE PAST, ARE YOU SURE YOU WANT TO SUBMIT'); ?>')) is_submit = false;
			}
		}
		if(is_submit) submitform( pressbutton );
	}

}

// This function gets called when an end-user clicks on some date
function selected(cal, date) {
	cal.sel.value = date; // just update the value of the input field
}

// And this gets called when the end-user clicks on the _selected_ date,
// or clicks the "Close" (X) button.  It just hides the calendar without
// destroying it.
function closeHandler(cal) {
	cal.hide();			// hide the calendar

	// don't check mousedown on document anymore (used to be able to hide the
	// calendar when someone clicks outside it, see the showCalendar function).
	Calendar.removeEvent(document, "mousedown", checkCalendar);
}
function showCalendar(id, dateFormat) {
	var el = document.getElementById(id);
	if (calendar != null) {
		// we already have one created, so just update it.
		calendar.hide();		// hide the existing calendar
		calendar.parseDate(el.value); // set it to a new date
	} else {
		// first-time call, create the calendar
		var cal = new Calendar(true, null, selected, closeHandler);
		calendar = cal;		// remember the calendar in the global
		cal.setRange(1900, 2070);	// min/max year allowed

		if ( dateFormat )	// optional date format
		{
			cal.setDateFormat(dateFormat);
		}

		calendar.create();		// create a popup calendar
		calendar.parseDate(el.value); // set it to a new date
	}
	calendar.sel = el;		// inform it about the input field in use
	calendar.showAtElement(el);	// show the calendar next to the input field

	// catch mousedown on the document
	Calendar.addEvent(document, "mousedown", checkCalendar);
	return false;
}


//-->
</script>

<form action="index.php" method="post" name="adminForm">

<table><tr valign="top"><td>

	<fieldset class="adminform">
	<legend><?php echo JText::_('COUPON DETAILS'); ?></legend>

	<table class="admintable">
		<tr><td class="key"><label><?php echo JText::_( 'COUPON CODE' ); ?></label></td>
			<td><input class="inputbox" type="text" name="coupon_code" size="40" maxlength="255" value="<?php echo $this->row->coupon_code; ?>" /></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'PUBLISHED' ); ?></label></td>
			<td><?php echo $this->lists['published']; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'FUNCTION TYPE' ); ?></label></td>
			<td><?php echo JText::_('COUPON'); ?></td>
		</tr>
		<tr><td class="key" nowrap><label><?php echo JText::_( 'NUMBER OF USES' ); ?></label></td>
			<td><?php echo $this->lists['num_of_uses_type']; ?>
				# <input class="inputbox" type="text" name="num_of_uses" size="2" maxlength="255" value="<?php echo $this->row->num_of_uses; ?>" />
			</td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'PERCENT OR TOTAL' ); ?></label></td>
			<td><?php echo $this->lists['coupon_value_type']; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'DISCOUNT TYPE' ); ?></label></td>
			<td><?php echo $this->lists['discount_type']; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'VALUE' ); ?></label></td>
			<td><input class="inputbox" type="text" name="coupon_value" size="40" maxlength="255" value="<?php echo $this->row->coupon_value; ?>" /></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'MINIMUM VALUE' ); ?></label></td>
			<td><input class="inputbox" type="text" name="min_value" size="40" maxlength="255" value="<?php echo $this->row->min_value; ?>" /></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'EXPIRATION' ); ?></label></td>
			<td><input class="inputbox" type="text" id="expiration" name="expiration" size="35" maxlength="255" value="<?php echo $this->row->expiration; ?>" />
				<input type="reset" class="button" value="..." onclick="return showCalendar( 'expiration', '%Y-%m-%d' );"/>
				(YYYY-MM-DD)
			</td>
		</tr>
	</table>

	</fieldset>
</td><td width="35%">

	<fieldset class="adminform" style="width:93%;height:300px;">
	<legend><?php echo JText::_('USERS'); ?></legend>
		<select name="userlist[]" class="inputbox" multiple size="7" style="width:100%; height:90%;">
		<?php foreach($this->userlist as $tmp) : ?>
			<option value="<?php echo $tmp->user_id; ?>" <?php echo isset($this->row->userlist[$tmp->user_id]) ? 'SELECTED' : ''; ?>>(<?php echo $tmp->user_id; ?>) <?php echo $tmp->last_name; ?> <?php echo $tmp->first_name; ?></option>
		<?php endforeach; ?>
		</select>
		<table cellpadding="0" cellspacing="0" width="100%"><tr>
			<td><i style="color:#777777;"><?php echo JText::_('CTRL SHIFT KEY'); ?></i></td>
			<td align="right"><input type="button" onclick="clearbox(1)" value="<?php echo JText::_('CLEAR'); ?>" /></td>
		</tr></table>
	</fieldset>
	
</td><td width="35%">

	<fieldset class="adminform" style="width:93%;height:300px;">
	<legend><?php echo JText::_('PRODUCTS'); ?></legend>
		<select name="productlist[]" class="inputbox" multiple size="7" style="width:100%; height:90%;">
		<?php foreach($this->productlist as $tmp) : ?>
			<option value="<?php echo $tmp->product_id; ?>" <?php echo isset($this->row->productlist[$tmp->product_id]) ? 'SELECTED' : ''; ?>><?php echo $tmp->product_name.' ('.$tmp->product_sku.')'; ?></option>
		<?php endforeach; ?>
		</select>
		<table cellpadding="0" cellspacing="0" width="100%"><tr>
			<td><i style="color:#777777;"><?php echo JText::_('CTRL SHIFT KEY'); ?></i></td>
			<td align="right"><input type="button" onclick="clearbox(2)" value="<?php echo JText::_('CLEAR'); ?>" /></td>
		</tr></table>
	</fieldset>

</td></tr></table>



<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_awocoupon" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
<input type="hidden" name="controller" value="coupons" />
<input type="hidden" name="view" value="coupon" />
<input type="hidden" name="cid[]" value="<?php echo $this->row->id; ?>" />
<input type="hidden" name="mask" value="0" />
</form>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>