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


var my_option = "com_awocoupon";
var base_url = "<?php echo JURI::base(true); ?>/index.php";



jQuery(document).ready(function() {
	funtion_type_change(true);
	
	jQuery( "#user_search" )
		.autocomplete({
			source: function( request, response ) {
				jQuery.getJSON( 
					base_url, 
					{option:my_option, task:'ajax_elements', type:'user', tmpl:'component', no_html:1,term: request.term}, 
					response 
				);
			},
			minLength: 2,
			selectFirst: true,
			delay:0,
			select: function( event, ui ) { if(ui.item) document.adminForm.user_id.value = ui.item.id; }
		})
		.attr("parameter_id", document.adminForm.user_id.value)
		.bind("empty_value", function(event){ document.adminForm.user_id.value = ''; })
		.bind("check_user", function(event){ return; })
	;

});



function funtion_type_change(is_edit) {
	var form = document.adminForm;
	is_edit = (is_edit == undefined) ? false : is_edit;
	
	if(!is_edit) { 
		view_some('asset');
		var tbl = document.getElementById('tbl_assets'); for(var i = tbl.rows.length - 1; i > 0; i--){ tbl.deleteRow(i);} 
	}
	form.asset_name.value = '';
	form.asset_id.value = '';
	//close_panes();



	jQuery('#pn_asset span, #title_assets').html(str_asset);
	jQuery('#pn_user').parent().show();
	jQuery('#pn_asset').parent().show();
	
	asset_type_change(is_edit);
	

	//open_pane('pn_asset');
	//setTimeout(function(){open_pane('pn_asset')},500)

}



var str_coup_err_valid_code = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_COUPON_CODE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE')); ?>';
var str_coup_err_valid_uses_type = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_NUMBER_USES_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE')); ?>';
var str_coup_err_valid_uses = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_NUMBER_USES').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE')); ?>';
var str_coup_err_confirm_expiration = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_ERR_EXPIRATION_PAST')); ?>';
var str_coup_err_value_type = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_VALUE_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE')); ?>';
var str_coup_err_valid_value = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_VALUE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE')); ?>';
var str_coup_err_choose_product = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_ERR_ONE_SPECIFIC')); ?>';
var str_coup_err_valid_min = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_VALUE_MIN').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE')); ?>';

var str_asset = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_ASSET')); ?>';

function clearbox(val) {
	var form = document.adminForm;
	if(val == 1) form.elements['userlist[]'].selectedIndex = -1;
}
function isUnsignedInteger(s) {
	return (s.toString().search(/^[0-9]+$/) == 0);
}
function trim(str, chars) {
	return ltrim(rtrim(str, chars), chars);
}
 
function ltrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}
 
function rtrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}

function submitbutton(pressbutton) {
	var form = document.adminForm;

	if (pressbutton == 'cancelcoupon') {
		submitform( pressbutton );
		return;
	}
	
	var err = '';
	if(trim(form.coupon_code.value)=='') err += '\n'+str_coup_err_valid_code;
	
	if(form.num_of_uses_type.value!='' && form.num_of_uses_type.value!='total' && form.num_of_uses_type.value!='per_user')  err += '\n'+str_coup_err_valid_uses_type;
	if(trim(form.num_of_uses.value)!='' && !isUnsignedInteger(form.num_of_uses.value)) err += '\n'+str_coup_err_valid_uses;
	if( (form.num_of_uses_type.value!='' && trim(form.num_of_uses.value)=='') || (form.num_of_uses_type.value=='' && trim(form.num_of_uses.value)!=''))  err += '\n'+str_coup_err_valid_uses;
	
	if(trim(form.coupon_value_type.value)=='') err += '\n'+str_coup_err_value_type;
	
	if(trim(form.discount_type.value)=='specific' && form.elements['assetlist[]']==undefined) err += '\n'+str_coup_err_choose_product;
					
					
	if(trim(form.coupon_value.value)=='' || isNaN(form.coupon_value.value) || form.coupon_value.value<0.01) err += '\n'+str_coup_err_valid_value; 
	if(trim(form.min_value.value)!='' && form.min_value.value!=0 && (isNaN(form.min_value.value) || form.min_value.value<0.01)) err += '\n'+str_coup_err_valid_min; 
	
	if(err != '') alert(err);
	else {
		var is_submit = true;
		if(trim(form.expiration.value)!='') {
			d = form.expiration.value.substr(0,4)+form.expiration.value.substr(5,2)+form.expiration.value.substr(8,2);
			d = d*1;
			if(d < <?php echo date('Ymd'); ?>) {
				if(!confirm(str_coup_err_confirm_expiration)) is_submit = false;
			}
		}
		if(is_submit) submitform( pressbutton );
	}

}

if( typeof Joomla != 'undefined' ){
	Joomla.submitbutton = submitbutton;
}



function view_all(type) {
	form = document.adminForm;

	if(type=='asset') {
		field = form.asset1_function_type.value;
		sel = form._assetlist; 
	} else if(type=='user') {
		field = 'user';
		sel = form._userlist; 
	} else return;


	if(field== '') return;
    if( typeof Joomla == 'undefined' ){
		jQuery('#pn_'+type).parent().find('div.jpane-slider').css({'height':'auto'}); // j15
	}
	
	jQuery('#div_'+type+'_simple_table').hide();
	jQuery('#div_'+type+'_advanced_table').show();
	
	
	jQuery.getJSON( 
		base_url, 
		{option:my_option, task:'ajax_elements_all', type:field, tmpl:'component', no_html:1}, 
		function(data) {
//alert(JSON.stringify(data));
			i=0;
			sel.options.length=0;
			jQuery.each(data, function(key, val) {
				sel.options[i++] = new Option(val.label,val.id);
			})
		}
	);
	
}
function view_some(type) {
	jQuery('#div_'+type+'_advanced_table').hide();
	jQuery('#div_'+type+'_simple_table').show();
}

function dd_itemselectf(type) {

	form = document.adminForm;
	if(type=='asset') {
		id = form.asset_id.value;
		name = form.asset_name.value;
		value_list_id = 'assetlist[]';
		value_list_name = 'assetnamelist['+id+']';
		tbl = 'tbl_assets';
	} else if(type == 'user') {
		id = form.user_id.value;
		name = form.user_name.value;
		value_list_id = 'userlist[]';
		value_list_name = 'usernamelist['+id+']';
		tbl = 'tbl_users';
	} else return;

	// set coupon to specific if new and assets are selected
	if(form.id.value=='' && form.elements[value_list_id]==undefined && tbl=='tbl_assets') form.discount_type.selectedIndex = 1;
	if(trim(id)!='') {

		// do not add duplicates
		valueDD = form.elements[value_list_id];
		if(valueDD!=undefined) {
			if(valueDD.value != undefined && valueDD.value==id) return;
			else {
				is_continue = false;
				for(j=0,len2=valueDD.length; j<len2; j++) if(valueDD[j].value==id) {is_continue = true; break;}
				if(is_continue) return;
			}
		}

		// add body
		jQuery('#'+tbl+' > tbody:last').append(
			'<tr id="'+tbl+'_tr'+id+'">'+
				'<td>'+id+'</td>'+
				'<td>'+name+'</td>'+
				'<td class="last" align="right">'+
						'<button type="button" onclick="deleterow(\''+tbl+'_tr'+id+'\');return false;" >X</button>'+
						'<input type="hidden" name="'+value_list_id+'" value="'+id+'">'+
						'<input type="hidden" name="'+value_list_name+'" value="'+name+'">'+
				'</td>'+
			'</tr>'
		); 
	}
	
	
}
function dd_itemselectg(type) {
	form = document.adminForm;
	if(type == 'asset') {
		value_list_id = 'assetlist[]';
		tbl = 'tbl_assets';
		searchDD = form.elements['_assetlist'];
	} else if(type== 'user') {
		value_list_id = 'userlist[]';
		tbl = 'tbl_users';
		searchDD = form.elements['_userlist'];
	} else return;
	
	// set coupon to specific if new and assets are selected
	if(form.elements[value_list_id]==undefined && tbl=='tbl_assets') form.discount_type.selectedIndex = 1;
	
	for(var i=0, len=searchDD.options.length;i<len;i++) {
		if(searchDD.options[i].selected) {
			id = searchDD.options[i].value;
			if(trim(id)=='') continue;

			name = searchDD.options[i].innerHTML;
	
			// do not add duplicates
			valueDD = form.elements[value_list_id];
			if(valueDD!=undefined) {
				if(valueDD.value != undefined && valueDD.value==id) continue;
				else {
					is_continue = false;
					for(j=0,len2=valueDD.length; j<len2; j++) if(valueDD[j].value==id) {is_continue = true; break;}
					if(is_continue) continue;
				}
			}
			// add body
			jQuery('#'+tbl+' > tbody:last').append(
				'<tr id="'+tbl+'_tr'+id+'">'+
					'<td>'+id+'</td>'+
					'<td>'+name+'</td>'+
					'<td class="last" align="right">'+
							'<button type="button" onclick="deleterow(\''+tbl+'_tr'+id+'\');return false;" >X</button>'+
							'<input type="hidden" name="'+value_list_id+'" value="'+id+'"></td>'+
							'<input type="hidden" name="'+type+'namelist['+id+']" value="'+name+'">'+
				'</tr>'
			); 
		}
	}
	
	
}
function dd_searchg(type) {
	if(type=='asset') {
		var input_text = 'asset_search_txt';
		var searchDD = document.adminForm.elements['_assetlist'];
	} else if(type=='user') {
		var input_text = 'user_search_txt';
		var searchDD = document.adminForm.elements['_userlist'];
	} else return;
	
	//searchDD.multiple = false;
	var input=document.getElementById(input_text).value.toLowerCase();
	if(trim(input)=='') { searchDD.selectedIndex = -1; return; }
	
	searchDD.selectedIndex = -1;
	var output = searchDD.options;
	for(var i=0, len=output.length;i<len;i++) { if(output[i].text.toLowerCase().indexOf(input)==0){ output[i].selected=true; break; } }
	
	//searchDD.multiple = true;
	
}


function deleterow(id) { var tr = document.getElementById(id); tr.parentNode.removeChild(tr); }

function close_panes() {
    if( typeof Joomla != 'undefined' ){
		jQuery("#extra_options .panel h3").removeClass("pane-toggler-down").addClass("pane-toggler");
		jQuery("#extra_options .panel div.pane-slider").hide();
	} else {
		jQuery("#extra_options .panel h3").removeClass("jpane-toggler-down").addClass("jpane-toggler");
		jQuery("#extra_options .panel div.mypane-slider").hide();
	}
}
function open_pane(pane) {	
    if( typeof Joomla != 'undefined' ){
		jQuery('#'+pane).removeClass("pane-toggler").addClass("pane-toggler-down");
		jQuery('#'+pane).parent().find(".pane-slider").css({height:"auto"}).show();
		//jQuery('#'+pane).parent().find(".pane-slider").css({height:"auto"}).show().addClass('pane-down').removeClass('pane-hide');
	} else { 
		jQuery('#'+pane).removeClass("jpane-toggler").addClass("jpane-toggler-down");
		jQuery('#'+pane).parent().find(".mypane-slider").show().css({"height":"auto"});

	}
	
}


function asset_type_change(is_edit) {
	var form = document.adminForm;
	
	is_edit = (is_edit == undefined) ? false : is_edit;
	
	val = form.asset1_function_type.value;		
	if(val=='') jQuery('#div_asset1_inner').hide();
	else {
		if(!is_edit) { var tbl = document.getElementById('tbl_assets'); for(var i = tbl.rows.length - 1; i > 0; i--){ tbl.deleteRow(i);}  }
		view_some('asset');
		jQuery( "#asset_search" )
			.autocomplete({
				source: function( request, response ) {
					jQuery.getJSON( 
						base_url, 
						{option:my_option, task:'ajax_elements', type:val, tmpl:'component', no_html:1,term: request.term}, 
						response 
					);
				},
				minLength: 2,
				selectFirst: true,
				delay:0,
				select: function( event, ui ) { if(ui.item) document.adminForm.asset_id.value = ui.item.id; }
			})
			.attr("parameter_id", document.adminForm.asset_id.value)
			.bind("empty_value", function(event){ document.adminForm.asset_id.value = ''; })
			.bind("check_user", function(event){ return; })
		;
		jQuery('#div_asset1_inner').show();
	}


}

function getjqdd(intype) {
}

//-->
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-validate">


	<div class="width-50 fltlft">
		<fieldset class="adminform">
		<legend><?php echo JText::_('COM_AWOCOUPON_CP_COUPON_DETAILS'); ?></legend>

		<table class="admintable">
			<tr>
				<td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_COUPON_CODE' ); ?></label></td>
				<td><input class="inputbox" type="text" name="coupon_code" size="30" maxlength="255" value="<?php echo $this->row->coupon_code; ?>" /></td>
			</tr>
			<tr>
				<td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' ); ?></label></td>
				<td><?php echo $this->lists['published']; ?></td>
			</tr>
			<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_FUNCTION_TYPE' ); ?></label></td>
				<td><?php echo JText::_('COM_AWOCOUPON_CP_COUPON'); ?></td>
			</tr>
			<tr>
				<td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_PERCENT_AMOUNT' ); ?></label></td>
				<td><?php echo $this->lists['coupon_value_type']; ?></td>
			</tr>
			<tr>
				<td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_DISCOUNT_TYPE' ); ?></label></td>
				<td><?php echo $this->lists['discount_type']; ?></td>
			</tr>
			<tr>
				<td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_VALUE' ); ?></label></td>
				<td><input class="inputbox" type="text" name="coupon_value" size="30" maxlength="255" value="<?php echo $this->row->coupon_value; ?>" /></td>
			</tr>

			<tr><td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_NUMBER_USES' ); ?></label></td>
				<td><?php echo $this->lists['num_of_uses_type']; ?>
					# <input class="inputbox" type="text" name="num_of_uses" size="2" maxlength="255" value="<?php echo $this->row->num_of_uses; ?>" />
				</td>
			</tr>
			<tr><td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_CP_VALUE_MIN' ); ?></label></td>
				<td><input class="inputbox" type="text" name="min_value" size="30" maxlength="255" value="<?php echo $this->row->min_value; ?>" /></td>
			</tr>
			<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_DATE_START' ); ?></label></td>
				<td><?php echo JHTML::calendar($this->row->startdate, 'startdate', 'startdate', '%Y-%m-%d',
									array('size'=>'12',
									'maxlength'=>'10',
								));
					?>
					(YYYY-MM-DD)
				</td>
			</tr>
			<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_EXPIRATION' ); ?></label></td>
				<td><?php echo JHTML::calendar($this->row->expiration, 'expiration', 'expiration', '%Y-%m-%d',
									array('size'=>'12',
									'maxlength'=>'10',
								));
					?>
					(YYYY-MM-DD)
				</td>
			</tr>
		</table>

		</fieldset>
	</div>

	<div class="width-50 fltrt">
		<?php
		if(version_compare(JVERSION,'1.6.0','ge')) {
			echo JHtml::_('sliders.start','extra_options', array('startOffset'=>1));
			echo JHtml::_('sliders.panel',JText::_('COM_AWOCOUPON_CP_CUSTOMERS'), 'pn_user');
		}  else {
			$sliders = &JPane::getInstance('sliders', array('allowAllClose' => true));
			echo $sliders->startPane('extra_options');
			echo $sliders->startPanel( JText::_('COM_AWOCOUPON_CP_CUSTOMERS'), 'pn_user' );

			//$tabs = JPane::getInstance('tabs');
			//echo $tabs->startPane( 'extra_options' );
			//echo $tabs->startPanel( JText::_("COM_AWOCOUPON_CP_CUSTOMERS"), 'pn_user');
		}
		?>

			
			<div id="div_users" style="padding:10px;">
				<div id="div_user_simple_table">
					<span style="width:70px;display:inline-block;"><?php echo JText::_('COM_AWOCOUPON_GBL_SEARCH'); ?>:</span>
					<input class="inputbox" type="text" id="user_search" name="user_name" size="60" maxlength="255" value="" />
					<input type="hidden" name="user_id" value="" />
					<button type="button" onclick="dd_itemselectf('user'); return false;"><?php echo JText::_('COM_AWOCOUPON_GBL_ADD'); ?></button>
					[ <a href="javascript:view_all('user');"><?php echo JText::_('COM_AWOCOUPON_CP_VIEW_ALL'); ?></a> ]
				</div>
				
				<div id="div_user_advanced_table" style="display:none;">
					<div>
						<span style="width:70px;display:inline-block;"><?php echo JText::_('COM_AWOCOUPON_GBL_SEARCH'); ?>:</span>
						<input type="text" id="user_search_txt" size="60" onkeyup="dd_searchg('user')">
						<button onclick="dd_itemselectg('user'); return false;"><?php echo JText::_('COM_AWOCOUPON_GBL_ADD'); ?></button>
						[ <a href="javascript:view_some('user');"><?php echo JText::_('COM_AWOCOUPON_CP_RETURN'); ?></a> ]
					</div>
					<select name="_userlist" MULTIPLE class="inputbox" size="2" style="width:100%; height:160px;" ondblclick="dd_itemselectg('user')"></select>
					<div style="color:#777777;"><i><?php echo JText::_('COM_AWOCOUPON_GBL_CTRL_SHIFT'); ?></i></div>
					<br />
				</div>
				
				<div class="function_type2_holder">
					<table id="tbl_users" class="adminlist" cellspacing="1">
					<thead><tr><th><?php echo JText::_('COM_AWOCOUPON_GBL_ID'); ?></th><th><?php echo JText::_('COM_AWOCOUPON_GBL_NAME'); ?></th><th>&nbsp;</th></tr></thead>
					<tbody>
					<?php  foreach($this->row->userlist as $row) { ?>
						<tr id="tbl_users_tr<?php echo $row->user_id; ?>">
							<td><?php echo $row->user_id; ?></td>
							<td><?php echo $row->user_name; ?></td>
							<td class="last" align="right">
								<button type="button" onclick="deleterow('tbl_users_tr<?php echo $row->user_id; ?>');return false;" >X</button>
								<input type="hidden" name="userlist[]" value="<?php echo $row->user_id; ?>">
								<input type="hidden" name="usernamelist[<?php echo $row->user_id; ?>]" value="<?php echo $row->user_name; ?>"></td>
						</tr>
					<?php } ?>
					</tbody></table>
				</div>
			</div>

			
			
			
			
			
			
			
			
				

		<?php
		if(version_compare(JVERSION,'1.6.0','ge')) {
			echo JHtml::_('sliders.panel',JText::_('COM_AWOCOUPON_CP_PRODUCTS'), 'pn_asset');
		} else {
			echo $sliders->endPanel();
			echo $sliders->startPanel( JText::_('COM_AWOCOUPON_CP_PRODUCTS'), 'pn_asset' );
			//echo $tabs->startPanel( JText::_('COM_AWOCOUPON_CP_PRODUCTS'), 'pn_asset');
		}
		?>


			
			<div style="padding:10px;">
				<div id="div_asset1_type">
					<span id="span_asset1_type">
						<span style="width:70px;display:inline-block;"><?php echo JText::_('COM_AWOCOUPON_GBL_TYPE'); ?></span>
						<?php echo $this->lists['asset1_function_type']; ?>
					</span>
				</div>
				
				<div id="div_asset1_inner" >
					<div id="div_asset_simple_table">
						<span style="width:70px;display:inline-block;"><?php echo JText::_('COM_AWOCOUPON_GBL_SEARCH'); ?>:</span>
						<input class="inputbox" type="text" id="asset_search" name="asset_name" size="60" maxlength="255" value="" />
						<input type="hidden" name="asset_id" value="" />
						<button type="button" onclick="dd_itemselectf('asset'); return false;"><?php echo JText::_('COM_AWOCOUPON_GBL_ADD'); ?></button>
						[ <a href="javascript:view_all('asset');"><?php echo JText::_('COM_AWOCOUPON_CP_VIEW_ALL'); ?></a> ]
					</div>
							
					<div id="div_asset_advanced_table" style="display:none;">
						<div>
							<span style="width:70px;display:inline-block;"><?php echo JText::_('COM_AWOCOUPON_GBL_SEARCH'); ?>:</span>
							<input type="text" id="asset_search_txt" size="60" onkeyup="dd_searchg('asset')">
							<button onclick="dd_itemselectg('asset'); return false;"><?php echo JText::_('COM_AWOCOUPON_GBL_ADD'); ?></button>
							[ <a href="javascript:view_some('asset');"><?php echo JText::_('COM_AWOCOUPON_CP_RETURN'); ?></a> ]
						</div>
						<select name="_assetlist" MULTIPLE class="inputbox" size="2" style="width:100%; height:160px;" ondblclick="dd_itemselectg('asset')"></select>
						<div style="color:#777777;"><i><?php echo JText::_('COM_AWOCOUPON_GBL_CTRL_SHIFT'); ?></i></div>
						<br />
					</div>
					
					<div class="function_type2_holder">
						<table id="tbl_assets" class="adminlist" cellspacing="1">
						<thead><tr><th><?php echo JText::_('COM_AWOCOUPON_GBL_ID'); ?></th><th><?php echo JText::_('COM_AWOCOUPON_GBL_NAME'); ?></th><th>&nbsp;</th></tr></thead>
						<tbody>
						<?php  foreach($this->row->assetlist as $row) { ?>
							<tr id="tbl_assets_tr<?php echo $row->asset_id; ?>">
								<td><?php echo $row->asset_id; ?></td>
								<td><?php echo $row->asset_name; ?></td>
								<td class="last" align="right">
									<button type="button" onclick="deleterow('tbl_assets_tr<?php echo $row->asset_id; ?>');return false;" >X</button>
									<input type="hidden" name="assetlist[]" value="<?php echo $row->asset_id; ?>">
									<input type="hidden" name="assetnamelist[<?php echo $row->asset_id; ?>]" value="<?php echo $row->asset_name; ?>"></td>
							</tr>
						<?php } ?>
						</tbody></table>
					</div>
				</div>
			</div>
					
			
			
			
			
			
			
			
			


		<?php
		if(version_compare(JVERSION,'1.6.0','ge')) {
			echo JHtml::_('sliders.end');
		} else {
			echo $sliders->endPanel();
			echo $sliders->endPane();
			//echo $tabs->endPane();
		}
		?>
				
	</div>


	<div class="clr"></div>










<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="<?php echo AWOCOUPON_OPTION; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
<input type="hidden" name="view" value="coupon" />
<input type="hidden" name="cid[]" value="<?php echo $this->row->id; ?>" />
<input type="hidden" name="mask" value="0" />
</form>
<div class="clr"></div>


<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>