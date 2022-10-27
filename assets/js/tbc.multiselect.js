jQuery(document).ready(function(){
	// Мультиселект для стран
	jQuery('#tbc_add_country').click(function() {
		jQuery('#set_country option:selected').each( function(el) {
			var disabled = jQuery(this).attr('disabled');
			if ( disabled == undefined ) {
				var active_option = jQuery(this)[0].outerHTML;
				jQuery(this).attr( 'disabled', true );
				jQuery('#active_country').append(active_option);
				jQuery('#active_country option').attr('selected', true);
			}
		});
	});
  
	jQuery('#tbc_remove_country').click(function() {
		jQuery('#active_country option:selected').each(function(el) {
			jQuery(this).remove(); 
		});
	});
	
	jQuery('#rule_action_save').click(function() {
		jQuery('#active_country option').attr('selected', true);
	});
	
	//Мультиселект для страниц/записей
	jQuery('#tbc_add_pages').click(function() {
		jQuery('#set_pages option:selected').each( function(el) {
			var disabled = jQuery(this).attr('disabled');
			if ( disabled == undefined ) {
				var active_option = jQuery(this)[0].outerHTML;
				jQuery(this).attr( 'disabled', true );
				jQuery('#active_pages').append(active_option);
				jQuery('#active_pages option').attr('selected', true);
			}
		});
	});
  
	jQuery('#tbc_remove_pages').click(function() {
		jQuery('#active_pages option:selected').each(function(el) {
			jQuery(this).remove(); 
		});
	});
	
	jQuery('#pages_action_save').click(function() {
		jQuery('#active_pages option').attr('selected', true);
	});
});