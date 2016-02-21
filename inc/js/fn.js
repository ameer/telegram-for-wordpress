jQuery("#twp_send_to_channel").click(function() {
	if (jQuery(this).prop("checked") == false){
		jQuery("#twp_fieldset").prop("disabled", true);
		jQuery("#twp_fieldset label").css("color", "grey")
	}
	else {
		jQuery("#twp_fieldset").prop("disabled", false);
		jQuery("#twp_fieldset label").css("color", "black")
	}
});
jQuery(document).ready(function(){
	if (jQuery('#twp_send_to_channel').prop("checked") == false){
		jQuery("#twp_fieldset").prop("disabled", true);
		jQuery("#twp_fieldset label").css("color", "grey")
	}
	else {
		jQuery("#twp_fieldset").prop("disabled", false);
		jQuery("#twp_fieldset label").css("color", "black")
	}
	jQuery('.patterns li').click(function(){
		jQuery('#twp_channel_pattern').textrange('insert', jQuery(this).text())
		jQuery('#twp_channel_pattern').textrange('setcursor', jQuery('#twp_channel_pattern').textrange('get', 'end'));
	})
})
jQuery("#twp_channel_pattern").keyup(function(){
	var str = jQuery(this).val();
	var preview = emojione.toImage(str);
	jQuery('#output').html(preview);
	jQuery(this).val(emojione.shortnameToUnicode(str));
})
function sendTest() {
	var api_token = jQuery('input[name=twp_api_token]').val(), h = '';
	if(api_token != '' ) {
		jQuery('#sendbtn').prop('disabled', true);
		jQuery('#sendbtn').text('<?php echo __("Please wait...", "twp-plugin") ?> ');
		if(jQuery("#twp_hashtag").val() != ''){
			var h = '<?php  echo get_option("twp_hashtag"); ?>';
		}
		var msg = h +'\n'+'<?php echo __("This is a test message", "twp-plugin") ?>';
		jQuery.post(ajaxurl, 
		{ 
			msg: msg , api_token: api_token, subject: 'm', action:'twp_ajax_test'
		}, function( data ) {
			alert(data.description);
			jQuery('#sendbtn').prop('disabled', false);
			jQuery('#sendbtn').text('<?php  echo __("Send now!", "twp-plugin") ?>');
		}, 
		'json'); 
	} else {
		alert(' <?php  echo __("api_token field is empty", "twp-plugin") ?>') 
	}
};
function channelTest() {
	var bot_token = jQuery('input[name=twp_bot_token]').val(), channel_username = jQuery('input[name=twp_channel_username]').val(), h = '';
	if(bot_token != '' && channel_username != '' ) {
		var c = confirm('<?php echo __("This will send a test message to your channel. Do you want to continue?", "twp-plugin") ?>');
		if( c == true ){ 
			jQuery('#channelbtn').prop('disabled', true);
			jQuery('#channelbtn').text('<?php echo __("Please wait...", "twp-plugin") ?> '); 
			if(jQuery('#twp_hashtag').val() != ''){
				var h = '<?php  echo get_option("twp_hashtag"); ?>';
			}
			var msg = h +'\n'+'<?php echo __("This is a test message", "twp-plugin") ?>';

			jQuery.post(ajaxurl, 
			{ 
				channel_username: channel_username, msg: msg , bot_token: bot_token, subject: 'c', action:'twp_ajax_test'
			}, function( data ) {
				jQuery('#channelbtn').prop('disabled', false);
				jQuery('#channelbtn').text('<?php  echo __("Send now!", "twp-plugin") ?>'); 
				alert((data.ok == true ? 'The message sent succesfully.' : data.description))}, 'json');
		}
	} else {
		alert(' <?php  echo __("bot token/channel username field is empty", "twp-plugin") ?>') 
	}
}
function botTest() {
	if(jQuery('input[name=twp_bot_token]').val() != '' ) {
		var bot_token = jQuery('input[name=twp_bot_token]').val();
		jQuery('#checkbot').prop('disabled', true);
		jQuery('#checkbot').text('<?php echo __("Please wait...", "twp-plugin") ?> ');
		jQuery.post(ajaxurl, 
		{ 
			bot_token: bot_token, subject: 'b', action:'twp_ajax_test'
		}, function( data ) {
			if (data != undefined && data.ok != false){
				jQuery('#bot_name').text(data.result.first_name + ' ' + (data.result.last_name == undefined ? ' ' :  data.result.last_name ) + '(@' + data.result.username + ')')
			}else {
				jQuery('#bot_name').text(data.description)
			}
			jQuery('#checkbot').prop('disabled', false);
			jQuery('#checkbot').text('<?php echo __("Check bot token", "twp-plugin") ?>');
		}, 'json'); 
	} else {
		alert(' <?php  echo __("bot token field is empty", "twp-plugin") ?>') 
	}
}
jQuery(document).ready(function() {
	currnetTab = sessionStorage.currnetTab || '#twp_tab1';
	jQuery('.tabs ' + currnetTab).fadeIn(400).siblings().hide();
	jQuery('a[href="'+currnetTab+'"]').parent('li').addClass('active').siblings().removeClass('active');
	jQuery('.tabs .tab-links a').on('click', function(e)  {
		var currentAttrValue = jQuery(this).attr('href');
		if(typeof(Storage) !== 'undefined') {
			sessionStorage.currnetTab = currentAttrValue;
		}

        // Show/Hide Tabs
        jQuery('.tabs ' + currentAttrValue).fadeIn(400).siblings().hide();

        // Change/remove current tab to active
        jQuery(this).parent('li').addClass('active').siblings().removeClass('active');

        e.preventDefault();
    });
});
jQuery("#floating_save_button").click(function(){
	jQuery("#twp_form").submit();  
});
(function(jQuery){jQuery.fn.jScroll=function(e){var f=jQuery.extend({},jQuery.fn.jScroll.defaults,e);return this.each(function(){var a=jQuery(this);var b=jQuery(window);var c=new location(a);b.scroll(function(){a.stop().animate(c.getMargin(b),f.speed)})});function location(d){this.min=d.offset().top;this.originalMargin=parseInt(d.css("margin-top"),10)||0;this.getMargin=function(a){var b=d.parent().height()-d.outerHeight();var c=this.originalMargin;if(a.scrollTop()>=this.min)c=c+f.top+a.scrollTop()-this.min;if(c>b)c=b;return({"marginTop":c+'px'})}}};jQuery.fn.jScroll.defaults={speed:"slow",top:10}})(jQuery);
jQuery(document).ready(function() {
	jQuery(function() {
		jQuery('#floating_save_button').jScroll({ top : 34, speed : 600 });
	});
});
jQuery(document).ready(function(){
	jQuery('.patterns li').click(function(){
		jQuery('#twp_channel_pattern').textrange('insert', jQuery(this).text())
		jQuery('#twp_channel_pattern').textrange('setcursor', jQuery('#twp_channel_pattern').textrange('get', 'end'));
	})
})