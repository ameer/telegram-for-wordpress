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
    currnetTab = sessionStorage.currnetTab || '#twp_tab1';
    $('.tabs ' + currnetTab).fadeIn(400).siblings().hide();
    $('a[href="' + currnetTab + '"]').parent('li').addClass('active').siblings().removeClass('active');
    $('.tabs .tab-links a').on('click', function (e) {
        var currentAttrValue = $(this).attr('href');
        if (typeof (Storage) !== 'undefined') {
            sessionStorage.currnetTab = currentAttrValue;
        }

        // Show/Hide Tabs
        $('.tabs ' + currentAttrValue).fadeIn(400).siblings().hide();

        // Change/remove current tab to active
        $(this).parent('li').addClass('active').siblings().removeClass('active');

        e.preventDefault();
    });
    $("#floating_save_button").click(function () {
        $("#twp_form").submit();
    });
    (function ($) {
        $.fn.jScroll = function (e) {
            var f = $.extend({}, $.fn.jScroll.defaults, e);
            return this.each(function () {
                var a = $(this);
                var b = $(window);
                var c = new location(a);
                b.scroll(function () {
                    a.stop().animate(c.getMargin(b), f.speed)
                })
            });

            function location(d) {
                this.min = d.offset().top;
                this.originalMargin = parseInt(d.css("margin-top"), 10) || 0;
                this.getMargin = function (a) {
                    var b = d.parent().height() - d.outerHeight();
                    var c = this.originalMargin;
                    if (a.scrollTop() >= this.min) c = c + f.top + a.scrollTop() - this.min;
                    if (c > b) c = b;
                    return ({
                        "marginTop": c + 'px'
                    })
                }
            }
        };
        $.fn.jScroll.defaults = {
            speed: "slow",
            top: 10
        }
    })($);
    $(function () {
        $('#floating_save_button').jScroll({
            top: 34,
            speed: 600
        });
    });
});
