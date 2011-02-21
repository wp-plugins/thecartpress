/**
 * This file is part of TheCartPress.
 * 
 * TheCartPress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TheCartPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TheCartPress.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Java Script for theCartPress Admin panels
 */

/**
 * To send ajax calls to action hook: wp_ajax_<action_name>
 */
function tcp_send_ajax_action(admin_url, button, action_name, parent_class, result_class) {
	var dom_parent = jQuery(button).closest('.' + parent_class);
	jQuery('.ajax-feedback', dom_parent).css('visibility', 'visible');
	jQuery.post(admin_url, { action: action_name }, function(data) {
			//alert("Data Loaded: " + data);
			jQuery('.ajax-feedback', dom_parent).css('visibility', 'hidden');
			jQuery('.' + result_class, dom_parent).text(data);
		}
	);
}

/**
 * Up an item in a select control
 */
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

/**
 * Down an item in a select control
 */
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

/**
 * Load items from a select control in a textbox
 */
function tcp_load_select_values_to_textbox(select_id, txt_id) {
	var txt = jQuery('#' + txt_id);
	txt.val('');
	jQuery('#' + select_id + ' option').each( function() {
		txt.val(txt.val() + '#' + jQuery(this).val());
	});
	if (txt.val().length > 0)
		txt.val(txt.val().substr(0, txt.val().length - 1));
}

//http://sites.google.com/site/abapexamples/javascript/luhn-validation
String.prototype.luhnCheck = function()
{
    var luhnArr = [[0,2,4,6,8,1,3,5,7,9],[0,1,2,3,4,5,6,7,8,9]], sum = 0;
    this.replace(/\D+/g,"").replace(/[\d]/g, function(c, p, o){
        sum += luhnArr[ (o.length-p)&1 ][ parseInt(c,10) ];
    });
    return (sum%10 === 0) && (sum > 0);
};

//European union
function tcp_select_eu(select_id) {
	var values = ['BE', 'BG', 'CZ', 'DK', 'DE', 'EE', 'IE', 'EL', 'ES', 'FR', 'IT', 'CY', 'LV', 'LT', 'LU', 'HU', 'MT', 'NL', 'AT', 'PL', 'PT', 'RO', 'SI', 'SK', 'FI', 'SE', 'UK']; //,'HR', 'IS', 'TR'];
	tcp_select_values(select_id, values);
}

//NAFTA
function tcp_select_nafta(select_id) {
	var values = ['CA', 'MX', 'US'];
	tcp_select_values(select_id, values);
}
//caricom
function tcp_select_caricom(select_id) {
	var values = ['AG', 'BB', 'BS', 'BZ', 'DM', 'GD', 'GY', 'HT', 'JM', 'KN', 'LC', 'VC', 'SR', 'TT', 'MS', 'AI', 'BM', 'KY', 'TC', 'VG'];
	tcp_select_values(select_id, values);
}

//mercasur
function tcp_select_mercasur(select_id) {
	var values = ['AR', 'BR', 'PY', 'UY', 'VE', 'BO', 'PE', 'CL', 'CO', 'EC'];
	tcp_select_values(select_id, values);
}

//For now is Mercasur + Caricom
function tcp_select_oea(select_id) {
	var values = ['AR', 'BR', 'PY', 'UY', 'VE', 'BO', 'PE', 'CL', 'CO', 'EC', 'AG', 'BB', 'BS', 'BZ', 'DM', 'GD', 'GY', 'HT', 'JM', 'KN', 'LC', 'VC', 'SR', 'TT', 'MS', 'AI', 'BM', 'KY', 'TC', 'VG'];
	tcp_select_values(select_id, values);
}

//CAN Comunidad Andina
function tcp_select_can(select_id) {
	var values = ['BO', 'CO', 'EC', 'PE'];
	tcp_select_values(select_id, values);
}

//African union
function tcp_select_au(select_id) {
	var values = ['DZ', 'AO', 'BJ', 'BW', 'BF', 'BI', 'CM', 'CV', 'CF', 'TD', 'KM', 'CD', 'CG', 'CI', 'DJ', 'EG', 'GQ', 'ER', 'ET', 'GA', 'GM', 'GH', 'GN', 'GW', 'KE', 'LS', 'LR', 'LY', 'MG', 'MW', 'ML', 'MR', 'MU', 'MZ', 'NA', 'NG', 'EH', 'ST', 'RW', 'SN', 'SC', 'SL', 'SO', 'ZA', 'SD', 'SZ', 'TZ', 'TG', 'TK', 'TN', 'UG', 'DJ', 'ZM', 'ZW'];
	tcp_select_values(select_id, values);
}

//APEC Asia-Pacifico Economic cooperation
function tcp_select_apec(select_id) {
	var values = ['AU', 'BN', 'CA', 'ID', 'JP', 'KR', 'MY', 'NZ', 'PH', 'SG', 'TH', 'US', 'TW', 'HK', 'CN', 'MX', 'PG', 'CL', 'PE', 'RU', 'VN'];
	tcp_select_values(select_id, values);
}

//ASEAN Association of Southeast Asian Nations
function tcp_select_asean(select_id) {
	var values = ['BN', 'KH', 'ID', 'LA', 'MY', 'MM', 'PH', 'SG', 'TH', 'VN'];
	tcp_select_values(select_id, values);
}

function tcp_select_values(select_id, values) {
	jQuery('#' + select_id + ' option').each(
		function() {
			if ( jQuery.inArray(this.value, values) > -1 ) {
				this.selected = true;
			} else {
				this.selected = false;
			}
		}
	);
}

function tcp_select_none(select_id) {
	jQuery('#' + select_id + ' option').each(
		function() {
			this.selected = false;
		}
	);	
}
