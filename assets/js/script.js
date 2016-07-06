// gt2gd script
var ttimer, tsec = 0, timer, sec = 0;
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
		
        window.clearInterval(ttimer);
        jQuery('.gt2gd-hstatus .gt2gd-time').text('00:00:00');
        ttimer = window.setInterval(function() {
            tsec++;
            jQuery('.gt2gd-hstatus .gt2gd-time').gt2gdTimer(tsec);
        }, 1000);
		geodir_gt2gd_start_conversion(GT2GD.first_item, $box, $btn, true);
	});
    
    jQuery.fn.gt2gdTimer = function(sec) {
        var h   = Math.floor(sec / 3600);
        var m = Math.floor((sec - (h * 3600)) / 60);
        var s = sec - (h * 3600) - (m * 60);
        if (h   < 10) {h   = "0"+h;}
        if (m < 10) {m = "0"+m;}
        if (s < 10) {s = "0"+s;}        
        jQuery(this).text(h+':'+m+':'+s);
    }
});
function geodir_gt2gd_start_conversion(item, $box, $btn, $first) {
    var $item = jQuery('#gt2gd-' + item, $box);
    $first = typeof $first !== 'undefined' && $first ? true : false;
    
    window.clearInterval(timer);
    sec = 0;
    jQuery('.gt2gd-time', $item).text('00:00:00');
    timer = window.setInterval(function() {
        sec++;
        jQuery('.gt2gd-time', $item).gt2gdTimer(sec);
    }, 1000);
    
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
                        window.clearInterval(ttimer);
                        window.clearInterval(timer);
                        jQuery($btn).closest('span').addClass('gt2gd-fmsg').html(GT2GD.msg_gt2gd_done);
					} else if (data.next) {
                        if (jQuery('#gt2gd-' + data.next, $box).hasClass('gt2gd-progress')) {
                            window.clearInterval(timer);
                            sec = 0;
                            jQuery('.gt2gd-time', jQuery('#gt2gd-' + data.next, $box)).text('00:00:00');
                            timer = window.setInterval(function() {
                                sec++;
                                jQuery('.gt2gd-time', jQuery('#gt2gd-' + data.next, $box)).gt2gdTimer(sec);
                            }, 1000);
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
                    if (parseInt($pb.attr('aria-valuemax')) > 0 && parseInt($pb.attr('aria-valuemax')) < $done) {
                        $done = parseInt($pb.attr('aria-valuemax'));
                    }
                    var isDone = false;
                    if ($done == parseInt($pb.attr('aria-valuemax'))) {
                        isDone = true;
                    }
                    
                    $pb.attr('data-transitiongoal', $done);
                    $pb.progressbar({display_text: 'center', use_percentage: false, done: function(){if(isDone) {$p.slideUp(2000);}}, transition_delay: 10, refresh_speed: 1, display_text: 'center', use_percentage: false, amount_format: function(part, total) { return Math.round(100 * part / total) +'% ( ' + part + ' / ' + total + ' )';}});
                        
					if (data.next != item) {
                        jQuery($item).removeClass('gt2gd-swait').addClass('gt2gd-s' + data.status);
                        jQuery('.gt2gd-status', $item).html(data.status_txt);
                    }
					
					if (data.next == 'done') {
                        window.clearInterval(ttimer);
                        window.clearInterval(timer);
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