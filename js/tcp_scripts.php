function tcp_select_up(select_id, txt_id) {
	jQuery('#' + select_id + ' option:selected').each( function() {
		var newPos = jQuery('#' + select_id + ' option').index(this) - 1;
		if (newPos > -1) {
			jQuery('#' + select_id + ' option').eq(newPos).before("<option value='"+jQuery(this).val()+"' selected='selected'>"+jQuery(this).text()+"</option>");
			jQuery(this).remove();
		}
	});
	tcp_load_select_values_to_textbox(select_id, txt_id);
}

function tcp_select_down(select_id, txt_id) {
	var countOptions = jQuery('#' + select_id + ' option').size();
	jQuery('#' + select_id + ' option:selected').each( function() {
		var newPos = jQuery('#' + select_id + ' option').index(this) + 1;
		if (newPos < countOptions) {
			jQuery('#' + select_id + ' option').eq(newPos).after("<option value='"+jQuery(this).val()+"' selected='selected'>"+jQuery(this).text()+"</option>");
			jQuery(this).remove();
		}
	});
	tcp_load_select_values_to_textbox(select_id, txt_id);
}

function tcp_load_select_values_to_textbox(select_id, txt_id) {
	var txt = jQuery('#' + txt_id);
	txt.val('');
	jQuery('#' + select_id + ' option').each( function() {
		txt.val(txt.val() + '#' + jQuery(this).val());
	});
	if (txt.val().length > 0)
		txt.val(txt.val().substr(0, txt.val().length - 1));
}
