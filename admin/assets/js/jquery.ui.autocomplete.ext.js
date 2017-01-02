

/*
 * jQuery UI Autocomplete Auto Select Extension
 *
 * Copyright 2010, Scott González (http://scottgonzalez.com)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * http://github.com/scottgonzalez/jquery-ui-extensions
 */
(function( $ ) {

$.ui.autocomplete.prototype.options.autoSelect = true;
$( ".ui-autocomplete-input" ).live( "blur", function( event ) {
	var autocomplete = $( this ).data( "ui-autocomplete" );
	if($(this).val().length<2) return;
	if ( !autocomplete.options.autoSelect || autocomplete.selectedItem ) { return; }

	var resultset = autocomplete.widget().children( ".ui-menu-item" );
	if(resultset.length==0 && $(this).val().length!=0 && !isNaN($(this).attr("parameter_id")) && $(this).attr("parameter_id")>0) {
	//editing
		return; 
	}

	$that = $(this);
	var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" );
	resultset.each(function() {
		var item = $( this ).data( "uiAutocompleteItem" );
		
		// select the first item
		autocomplete.selectedItem = item;
		$that.val(item.label);
		return false;
		
		if ( matcher.test( item.label || item.value || item ) ) {
			autocomplete.selectedItem = item;
			return false;
		}
	});
	if ( autocomplete.selectedItem ) {
		autocomplete._trigger( "select", event, { item: autocomplete.selectedItem } );
		//$('li.ui-menu-item').remove();
	} else {
		$(this).val('');
		$(this).trigger("empty_value");
	}
});

}( jQuery ));


