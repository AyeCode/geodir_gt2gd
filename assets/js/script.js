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
		
		geodir_gt2gd_start_conversion(GT2GD.first_item, $box, $btn, true);
	});
});
function geodir_gt2gd_start_conversion(item, $box, $btn, $first) {
	var $item = jQuery('#gt2gd-' + item, $box);
    $first = typeof $first !== 'undefined' && $first ? true : false;
    
    if ($item.hasClass('gt2gd-progress')) {
        return geodir_gt2gd_batch_conversion(item, $box, $btn, $first, 0);
    }
    
	jQuery.ajax({
		url: ajaxurl,
		type: 'POST',
		data: 'action=gt2gd_ajax&task=convert&_item=' + item + '&_nonce=' + GT2GD.nonce + '&_f=' + ($first ? 1 : 0),
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
                    if (data.time !== 'undefined') {
                        jQuery('.gt2gd-status', $item).before('<span class="gt2gd-time">' + data.time + ' sec</span>');
                    }
					
					if (data.next == 'done') {
						jQuery('.gt2gd-hstatus .gt2gd-status').before('<span class="gt2gd-time">' + data.timet + ' sec</span>');
                        jQuery($btn).closest('span').addClass('gt2gd-fmsg').html(GT2GD.msg_gt2gd_done);
					} else if (data.next) {
                        if (jQuery('#gt2gd-' + data.next, $box).hasClass('gt2gd-progress')) {
                            geodir_gt2gd_batch_conversion(data.next, $box, $btn, $first, 0);
                        } else {
                            geodir_gt2gd_start_conversion(data.next, $box, $btn);
                        }
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

function geodir_gt2gd_batch_conversion(item, $box, $btn, $first, $done) {
    var $item = jQuery('#gt2gd-' + item, $box);
    var $p = jQuery('.progress', $item);
    var $pb = jQuery('.progress-bar', $item);
	
    jQuery.ajax({
		url: ajaxurl,
		type: 'POST',
		data: 'action=gt2gd_ajax&task=convert&_item=' + item + '&_nonce=' + GT2GD.nonce + '&_batch=1&_f=' + ($first ? 1 : 0),
		dataType: 'json',
		cache: false,
		beforeSend: function (jqXHR, settings) {
            jQuery($btn, $box).prop('disabled', true);
            jQuery($item).removeClass('gt2gd-sstart').addClass('gt2gd-swait');
            jQuery('.gt2gd-status', $item).html(GT2GD.txt_converting);
            
            if (!$p.is(':visible')) {
                $p.slideDown(100);
                $pb.attr('data-transitiongoal', $done);
                $pb.progressbar({display_text: 'center', use_percentage: false});
            }
		},
		success: function(data) {
			if (typeof data == 'object' && data) {
				if (data.error) {
					if (item == GT2GD.first_item) {
						jQuery($btn, $box).prop('disabled', false);
					}
					
					alert(data.error);
				} else {
                    $done = parseInt($done + data.done);
                    $pb.attr('data-transitiongoal', $done);
                    $pb.progressbar({display_text: 'center', use_percentage: false});
                        
					if (data.next != item) {
                        jQuery($item).removeClass('gt2gd-swait').addClass('gt2gd-s' + data.status);
                        jQuery('.gt2gd-status', $item).html(data.status_txt);
                        if (data.time) {
                            jQuery('.gt2gd-status', $item).before('<span class="gt2gd-time">' + data.time + ' sec</span>');
                        }
                    }
					
					if (data.next == 'done') {
						jQuery('.gt2gd-hstatus .gt2gd-status').before('<span class="gt2gd-time">' + data.timet + ' sec</span>');
                        jQuery($btn).closest('span').addClass('gt2gd-fmsg').html(GT2GD.msg_gt2gd_done);
					} else if (data.next) {
                        if (data.next == item) {
                            geodir_gt2gd_batch_conversion(item, $box, $btn, false, $done);
                        } else {
                            geodir_gt2gd_start_conversion(data.next, $box, $btn);
                        }
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