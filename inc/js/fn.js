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
	var objDiv = document.getElementById("output");
	objDiv.scrollTop = objDiv.scrollHeight;
})
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