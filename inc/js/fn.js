jQuery(document).ready(function ($) {
    
    /*
     * Select and upload custom image using WordPress Media dialog
     * from https://codex.wordpress.org/Javascript_Reference/wp.media
     */
    // Set all variables to be used in scope
    var frame,
        metaBox = $('#twp_meta_box.postbox'), // Your meta box id here
        addImgLink = metaBox.find('.upload-custom-img'),
        delImgLink = metaBox.find('.delete-custom-img'),
        imgContainer = metaBox.find('.twp-img-container'),
        imgIdInput = metaBox.find('.twp-img-id');
    // ADD IMAGE LINK
    addImgLink.on('click', function (event) {
        event.preventDefault();
        // If the media frame already exists, reopen it.
        if (frame) {
            frame.open();
            return;
        }
        // Create a new media frame
        frame = wp.media({
            title: twp_js_obj.frame_title,
            button: {
                text: twp_js_obj.button_text
            },
            library: {
                type: 'image'
            },
            multiple: false // Set to true to allow multiple files to be selected
        });
        // When an image is selected in the media frame...
        frame.on('select', function () {

            // Get media attachment details from the frame state
            var attachment = frame.state().get('selection').first().toJSON();

            // Send the attachment URL to our custom image input field.
            imgContainer.append('<img src="' + attachment.sizes.thumbnail.url + '" alt="" style="max-width:100%;"/>');

            // Send the attachment id to our hidden input
            imgIdInput.val(attachment.id);

            // Hide the add image link
            addImgLink.addClass('hidden');

            // Unhide the remove image link
            delImgLink.removeClass('hidden');
        });

        // Finally, open the modal on click
        frame.open();
    });
    // DELETE IMAGE LINK
    delImgLink.on('click', function (event) {

        event.preventDefault();

        // Clear out the preview image
        imgContainer.html('');

        // Un-hide the add image link
        addImgLink.removeClass('hidden');

        // Hide the delete image link
        delImgLink.addClass('hidden');

        // Delete the image id from the hidden input
        imgIdInput.val('');

    });
    // File variables
    var file_frame,
    addFileLink = metaBox.find('.upload-custom-file'),
    delFileLink = metaBox.find('.delete-custom-file'),
    fileContainer = metaBox.find('.twp-file-container'),
    fileIcon =  metaBox.find('#twp-file-icon'),
    fileDetails =  metaBox.find('#twp-file-details'),
    fileIdInput = metaBox.find('.twp-file-id');
    if (wp.media != undefined) {
        file_frame = wp.media({
            title: twp_js_obj.file_frame_title,
            button: {
                text: twp_js_obj.file_button_text
            },
            library: {},
            multiple: false
        });
        file_frame.on("select", function() {
            var a = file_frame.state().get("selection").first().toJSON();
            console.log(a)
            var b = a;
            console.log(b);
            if (b.filesizeInBytes > 25e6) var c = '<p style="color:#FFA700">Warning! The file size is larger than <strong> 25 MB </strong>. It may cause unexpected errors. </p>'; else if (b.filesizeInBytes > 5e7) var c = '<p style="color:#FF0000">Error! The file size is larger than <strong> 50 MB </strong>. Telegram Bots can currently send files of up to 50 MB. Please select a smaller file.</p>'; else var c = "";
            fileIcon.html("");
            fileDetails.html("");
            fileIcon.append('<a href="#" title="' + b.filename + '"><img src="' + b.icon + '" alt="' + b.filename + '" style="max-width:100%;"/></a>');
            fileDetails.append("<p>Title: <span>" + b.filename + "</span></p><p>Caption: <span>" + b.caption + "</span></p><p>Size: <span>" + b.filesizeHumanReadable + "</span></p>" + c);
            fileIdInput.val(a.id);
            addFileLink.addClass("hidden");
            delFileLink.removeClass("hidden");
        });
        file_frame.on("open", function() {
            var a = file_frame.state().get("selection");
            id = jQuery("input[name=twp_file_id]").val();
            if (id) {
                attachment = wp.media.attachment(id);
                attachment.fetch();
                a.add(attachment ? [ attachment ] : []);
            }
        });
        fileIcon.on("click", function(a) {
            a.preventDefault();
            file_frame.open();
        });
        addFileLink.on("click", function(a) {
            a.preventDefault();
            file_frame.open();
        });
        delFileLink.on("click", function(a) {
            a.preventDefault();
            fileIcon.html("");
            fileDetails.html("");
            addFileLink.removeClass("hidden");
            delFileLink.addClass("hidden");
            fileIdInput.val("");
        });
    }
    $("input[name=twp_send_thumb]").change(function () {
        if ($(this).val() == 2) {
            $("#twp-upload-link").removeClass("hidden");
        } else {
            $("#twp-upload-link").addClass("hidden");
            // Clear out the preview image
            imgContainer.html('');

            // Un-hide the add image link
            addImgLink.removeClass('hidden');

            // Hide the delete image link
            delImgLink.addClass('hidden');

            // Delete the image id from the hidden input
            imgIdInput.val('');
        }
    })

    $("input[name=twp_markdown]").change(function () {
        if ($(this).val() == 0) {
            $("input:radio[name=twp_img_position][id=twp-img-0]").prop('checked', true);
            $("input:radio[name=twp_img_position][id=twp-img-1]").prop('disabled', true);
            $("#twp-img-1-error").removeClass('hidden');
        } else {
            $("input:radio[name=twp_img_position][id=twp-img-1]").prop('disabled', false);
            $("#twp-img-1-error").addClass('hidden');
        }
    })

    $("#twp_send_to_channel").click(function () {
        if ($(this).prop("checked") == false) {
            $("#twp_fieldset").prop("disabled", true);
            $("#twp_fieldset label").css("color", "grey")
        } else {
            $("#twp_fieldset").prop("disabled", false);
            $("#twp_fieldset label").css("color", "black")
        }
    });

    if ($('#twp_send_to_channel').prop("checked") == false) {
        $("#twp_fieldset").prop("disabled", true);
        $("#twp_fieldset label").css("color", "grey")
    } else {
        $("#twp_fieldset").prop("disabled", false);
        $("#twp_fieldset label").css("color", "black")
    }
    $('#patterns-select').change(function () {
        var tcp = '#twp_channel_pattern';
        $(tcp).textrange('insert', $(this).val())
        $(tcp).textrange('setcursor', $(tcp).textrange('get', 'end'));
        var str = $(tcp).val();
        var preview = emojione.toImage(str);
        $('#output').html(preview);
    })
    $('#emoji-select').change(function () {
        var tcp = '#twp_channel_pattern';
        $(tcp).textrange('insert', $(this).val())
        $(tcp).textrange('setcursor', $(tcp).textrange('get', 'end'));
        var str = $(tcp).val();
        var preview = emojione.toImage(str);
        $('#output').html(preview);
    })
    $("#twp_channel_pattern").keyup(function () {
        var str = $(this).val();
        var preview = emojione.toImage(str);
        $('#output').html(preview);
        $(this).val(emojione.shortnameToUnicode(str));
        var objDiv = document.getElementById("output");
        objDiv.scrollTop = objDiv.scrollHeight;
    })
    $('ul.tab').each(function(){
        // For each set of tabs, we want to keep track of
        // which tab is active and it's associated content
        var $active, $content, $links = $(this).find('a');

        // If the location.hash matches one of the links, use that as the active tab.
        // If no match is found, use the first link as the initial active tab.
        $active = $($links.filter('[href="'+sessionStorage.currentTab+'"]')[0] || $links[0]);
        $active.addClass('active');

        $content = $($active[0].hash);

        // Hide the remaining content
        $links.not($active).each(function () {
            $(this.hash).hide();
        });

        // Bind the click event handler
        $(this).on('click', 'a', function(e){
            if (typeof (Storage) !== 'undefined' && this.hash.startsWith("#twp")) {
                sessionStorage.currentTab = this.hash;
            }
            // Make the old tab inactive.
            $active.removeClass('active');
            $content.hide();

            // Update the variables with the new link and content
            $active = $(this);
            $content = $(this.hash);

            // Make the tab active.
            $active.addClass('active');
            $content.fadeIn(400);

            //Prevent the anchor's default click action
            e.preventDefault();
        });
    });
    $('#bot_info_chip').siblings('figure').attr('data-initial', $('#bot_info_chip').text().substr(0,2))
    $("#checkbot").click(function() {
        if($('input[name=twp_bot_token]').val() != '' ) {
            var bot_token = $('input[name=twp_bot_token]').val();
            $('#checkbot').toggleClass('loading');
            $.post(ajaxurl, 
            { 
                bot_token: bot_token, subject: 'b', action:'twp_ajax_test'
            }, function( data ) {
                if (data != undefined && data.ok != false){
                    $('#bot_info_chip').text(data.result.first_name + " (@"+data.result.username+")").siblings('figure').attr('data-initial', data.result.first_name.substr(0,2))
                    $('input[name=twp_bot_name]').val(data.result.first_name);
                    $('input[name=twp_bot_username]').val("@"+data.result.username);
                    toastr["success"]("Token was successfully authorized.", "Success!")
                }else {
                    $('#bot_info_chip').text("Bot Name").siblings('figure').attr('data-initial', "N/A")
                    $('input[name=twp_bot_name]').val("");
                    $('input[name=twp_bot_username]').val("");
                    toastr["error"](data.description, "Oops! Error")
                }
                $('#checkbot').toggleClass('loading');
            }, 'json'); 
        } else {
            alert(twp_js_obj.bot_token_empty) 
        }
    });
    $('#send_test_msg').click(function () {
        var bot_token = $('input[name=twp_bot_token]').val(), chat_id = $('input[name=twp_chat_id]').val()  , h = '';
        if(bot_token != '' && chat_id != '') {
            $('#send_test_msg').toggleClass('loading');
            if($("#twp_hashtag").val() != ''){
                var h = $('#twp_hashtag').val();
            }
            var text = h +'\n'+twp_js_obj.test_message+ '\n' + document.URL;
            $.post(ajaxurl, 
            { 
                chat_id: chat_id, text: text , bot_token: bot_token, subject: 'm', action:'twp_ajax_test'
            }, function( data ) {

                if (data != undefined && data.ok != false){
                    var res = data.result;
                    if(res.chat.type == "private"){
                        $('#user_info').text(res.chat.first_name + " " + res.chat.last_name);
                    } else {
                        $('#user_info').text(res.chat.title + " " + res.chat.last_name);
                    }

                }else {
                    alert(data.ok);
                    $('#bot_name').text(data.description)
                }


                $('#send_test_msg').toggleClass('loading');
            }, 
            'json'); 
        } else {
            alert(twp_js_obj.token_chat_id_empty) 
        }
    });
    $('#channelbtn').click(function() { 
        var bot_token = $('input[name=twp_bot_token]').val(), channel_username = $('input[name=twp_channel_username]').val(), pattern = $("#twp_channel_pattern").val();
        if(bot_token != '' && channel_username != '' ) {
            var c = confirm(twp_js_obj.channel_test);
            if( c == true ){ 
                $('#channelbtn').prop('disabled', true);
                $('#channelbtn').toggleClass('loading'); 
                var msg = '<?php echo __("This is a test message", "twp-plugin") ?>'+'\n';
                if (pattern != null || pattern != ''){
                    msg += pattern;
                }
                $.post(ajaxurl, 
                { 
                    channel_username: channel_username, msg: msg , bot_token: bot_token, markdown: $('input[name=twp_markdown]:checked').attr("data-markdown"), web_preview: $('#twp_web_preview').prop('checked'), subject: 'c', action:'twp_ajax_test'
                }, function( data ) {
                    $('#channelbtn').prop('disabled', false);
                    $('#channelbtn').text('<?php  echo __("Send now!", "twp-plugin") ?>'); 
                    alert((data.ok == true ? 'The message sent succesfully.' : data.description))}, 'json');
            }
        } else {
            alert(' <?php  echo __("bot token/channel username field is empty", "twp-plugin") ?>') 
        }
    });
    $('#get_members_count').click(function() {
        var channel_username = $('input[name=twp_channel_username]').val();
        var bot_token = $('input[name=twp_bot_token]').val();
        if(channel_username !== '' && bot_token !== '') {
            $.post(ajaxurl, 
            { 
                bot_token: bot_token, channel_username: channel_username, subject: 'gm', action:'twp_ajax_test'
            }, function( data ) {
                if (data != undefined && data.ok != false){
                    $('#members_count').text(data.result+ " " +"<?php echo __('members', 'twp-plugin'); ?>");
                }else {
                    $('#members_count').text(data.description);
                }
            }, 'json'); 
        } else {
            return;
        }
    })
    $("#floating_save_button").click(function () {
        $("#twp_form").submit();
    });
});
/* Toaster */
!function(e){e(["jquery"],function(e){return function(){function t(e,t,n){return g({type:O.error,iconClass:m().iconClasses.error,message:e,optionsOverride:n,title:t})}function n(t,n){return t||(t=m()),v=e("#"+t.containerId),v.length?v:(n&&(v=d(t)),v)}function o(e,t,n){return g({type:O.info,iconClass:m().iconClasses.info,message:e,optionsOverride:n,title:t})}function s(e){C=e}function i(e,t,n){return g({type:O.success,iconClass:m().iconClasses.success,message:e,optionsOverride:n,title:t})}function a(e,t,n){return g({type:O.warning,iconClass:m().iconClasses.warning,message:e,optionsOverride:n,title:t})}function r(e,t){var o=m();v||n(o),u(e,o,t)||l(o)}function c(t){var o=m();return v||n(o),t&&0===e(":focus",t).length?void h(t):void(v.children().length&&v.remove())}function l(t){for(var n=v.children(),o=n.length-1;o>=0;o--)u(e(n[o]),t)}function u(t,n,o){var s=!(!o||!o.force)&&o.force;return!(!t||!s&&0!==e(":focus",t).length)&&(t[n.hideMethod]({duration:n.hideDuration,easing:n.hideEasing,complete:function(){h(t)}}),!0)}function d(t){return v=e("<div/>").attr("id",t.containerId).addClass(t.positionClass),v.appendTo(e(t.target)),v}function p(){return{tapToDismiss:!0,toastClass:"toast",containerId:"toast-container",debug:!1,showMethod:"fadeIn",showDuration:300,showEasing:"swing",onShown:void 0,hideMethod:"fadeOut",hideDuration:1e3,hideEasing:"swing",onHidden:void 0,closeMethod:!1,closeDuration:!1,closeEasing:!1,closeOnHover:!0,extendedTimeOut:1e3,iconClasses:{error:"toast-error",info:"toast-info",success:"toast-success",warning:"toast-warning"},iconClass:"toast-info",positionClass:"toast-top-right",timeOut:5e3,titleClass:"toast-title",messageClass:"toast-message",escapeHtml:!1,target:"body",closeHtml:'<button type="button">&times;</button>',closeClass:"toast-close-button",newestOnTop:!0,preventDuplicates:!1,progressBar:!1,progressClass:"toast-progress",rtl:!1}}function f(e){C&&C(e)}function g(t){function o(e){return null==e&&(e=""),e.replace(/&/g,"&amp;").replace(/"/g,"&quot;").replace(/'/g,"&#39;").replace(/</g,"&lt;").replace(/>/g,"&gt;")}function s(){c(),u(),d(),p(),g(),C(),l(),i()}function i(){var e="";switch(t.iconClass){case"toast-success":case"toast-info":e="polite";break;default:e="assertive"}I.attr("aria-live",e)}function a(){E.closeOnHover&&I.hover(H,D),!E.onclick&&E.tapToDismiss&&I.click(b),E.closeButton&&j&&j.click(function(e){e.stopPropagation?e.stopPropagation():void 0!==e.cancelBubble&&e.cancelBubble!==!0&&(e.cancelBubble=!0),E.onCloseClick&&E.onCloseClick(e),b(!0)}),E.onclick&&I.click(function(e){E.onclick(e),b()})}function r(){I.hide(),I[E.showMethod]({duration:E.showDuration,easing:E.showEasing,complete:E.onShown}),E.timeOut>0&&(k=setTimeout(b,E.timeOut),F.maxHideTime=parseFloat(E.timeOut),F.hideEta=(new Date).getTime()+F.maxHideTime,E.progressBar&&(F.intervalId=setInterval(x,10)))}function c(){t.iconClass&&I.addClass(E.toastClass).addClass(y)}function l(){E.newestOnTop?v.prepend(I):v.append(I)}function u(){if(t.title){var e=t.title;E.escapeHtml&&(e=o(t.title)),M.append(e).addClass(E.titleClass),I.append(M)}}function d(){if(t.message){var e=t.message;E.escapeHtml&&(e=o(t.message)),B.append(e).addClass(E.messageClass),I.append(B)}}function p(){E.closeButton&&(j.addClass(E.closeClass).attr("role","button"),I.prepend(j))}function g(){E.progressBar&&(q.addClass(E.progressClass),I.prepend(q))}function C(){E.rtl&&I.addClass("rtl")}function O(e,t){if(e.preventDuplicates){if(t.message===w)return!0;w=t.message}return!1}function b(t){var n=t&&E.closeMethod!==!1?E.closeMethod:E.hideMethod,o=t&&E.closeDuration!==!1?E.closeDuration:E.hideDuration,s=t&&E.closeEasing!==!1?E.closeEasing:E.hideEasing;if(!e(":focus",I).length||t)return clearTimeout(F.intervalId),I[n]({duration:o,easing:s,complete:function(){h(I),clearTimeout(k),E.onHidden&&"hidden"!==P.state&&E.onHidden(),P.state="hidden",P.endTime=new Date,f(P)}})}function D(){(E.timeOut>0||E.extendedTimeOut>0)&&(k=setTimeout(b,E.extendedTimeOut),F.maxHideTime=parseFloat(E.extendedTimeOut),F.hideEta=(new Date).getTime()+F.maxHideTime)}function H(){clearTimeout(k),F.hideEta=0,I.stop(!0,!0)[E.showMethod]({duration:E.showDuration,easing:E.showEasing})}function x(){var e=(F.hideEta-(new Date).getTime())/F.maxHideTime*100;q.width(e+"%")}var E=m(),y=t.iconClass||E.iconClass;if("undefined"!=typeof t.optionsOverride&&(E=e.extend(E,t.optionsOverride),y=t.optionsOverride.iconClass||y),!O(E,t)){T++,v=n(E,!0);var k=null,I=e("<div/>"),M=e("<div/>"),B=e("<div/>"),q=e("<div/>"),j=e(E.closeHtml),F={intervalId:null,hideEta:null,maxHideTime:null},P={toastId:T,state:"visible",startTime:new Date,options:E,map:t};return s(),r(),a(),f(P),E.debug&&console&&console.log(P),I}}function m(){return e.extend({},p(),b.options)}function h(e){v||(v=n()),e.is(":visible")||(e.remove(),e=null,0===v.children().length&&(v.remove(),w=void 0))}var v,C,w,T=0,O={error:"error",info:"info",success:"success",warning:"warning"},b={clear:r,remove:c,error:t,getContainer:n,info:o,options:{},subscribe:s,success:i,version:"2.1.4",warning:a};return b}()})}("function"==typeof define&&define.amd?define:function(e,t){"undefined"!=typeof module&&module.exports?module.exports=t(require("jquery")):window.toastr=t(window.jQuery)});
//# sourceMappingURL=toastr.js.map
toastr.options = {
  "closeButton": true,
  "debug": false,
  "newestOnTop": false,
  "progressBar": false,
  "positionClass": "toast-bottom-center",
  "preventDuplicates": false,
  "onclick": null,
  "showDuration": "1000",
  "hideDuration": "1000",
  "timeOut": "5000",
  "extendedTimeOut": "5000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "slideDown",
  "hideMethod": "fadeOut"
}