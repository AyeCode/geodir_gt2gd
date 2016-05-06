// gt2gd script
jQuery(function() {
	jQuery('#gt2gd_submit').click(function() {
		var $btn = this;
		var $box = jQuery($btn).closest('.meta-box-sortables').find('.gt2gd-box');
		
		if (!GT2GD.first_item) {
			alert(GT2GD.msg_completed);
			return false;
		}
		
		if (!confirm(GT2GD.msg_confirm)) {
			return false;
		}
		
		geodir_gt2gd_start_conversion(GT2GD.first_item, $box, $btn);
	});
});
function geodir_gt2gd_start_conversion(item, $box, $btn) {
	var $item = jQuery('#gt2gd-' + item, $box);
	jQuery.ajax({
		url: ajaxurl,
		type: 'POST',
		data: 'action=gt2gd_ajax&task=convert&_item=' + item + '&_nonce=' + GT2GD.nonce,
		dataType: 'json',
		cache: false,
		beforeSend: function (jqXHR, settings) {
			//console.log('beforeSend');
			jQuery($btn, $box).prop('disabled', true);
			jQuery($item).removeClass('gt2gd-sstart').addClass('gt2gd-swait');
			jQuery('.gt2gd-status', $item).html(GT2GD.txt_converting);
		},
		success: function(data) {
			if (typeof data == 'object' && data) {
				if (data.error) {
					if (item == GT2GD.first_item) {
						jQuery($btn, $box).prop('disabled', false);
					}
					
					alert(data.error);
				} else {
					jQuery($item).removeClass('gt2gd-swait').addClass('gt2gd-s' + data.status);
					jQuery('.gt2gd-status', $item).html(data.status_txt);
					
					if (data.next == 'done') {
						jQuery($btn).closest('span').addClass('gt2gd-fmsg').html(GT2GD.msg_gt2gd_done);
					} else if (data.next) {
						geodir_gt2gd_start_conversion(data.next, $box, $btn);
					}
				}
			} else {
				if (item == GT2GD.first_item) {
					jQuery($btn, $box).prop('disabled', false);
				}
			}
		},
		error: function( data ) {
			if (item == GT2GD.first_item) {
				jQuery($btn, $box).prop('disabled', false);
			}
		},
		complete: function( jqXHR, textStatus  ) {
			if (item == GT2GD.first_item) {
				jQuery($btn, $box).prop('disabled', false);
			}
		}
	});
}