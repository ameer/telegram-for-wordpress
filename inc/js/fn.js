jQuery(document).ready(function ($) {
    /**
     * Converts Bytes to Human readable
     * @param  {int}
     * @return {string}
     */
    function bytesToSize(bytes) {
       var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
       if (bytes == 0) return '0 Byte';
       var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
       return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
   };
   var filesize = $("input[name=filesize]").val();
   if (filesize > 0){
    $("#filesize-span").html(bytesToSize(filesize));
   }
    /*
     * Select and upload custom image using WordPress Media dialog
     * from https://codex.wordpress.org/Javascript_Reference/wp.media
     */
    // Set all variables to be used in scope
    var frame, file_frame,
        metaBox = $('#twp_meta_box.postbox'), // Your meta box id here
        addImgLink = metaBox.find('.upload-custom-img'),
        delImgLink = metaBox.find('.delete-custom-img'),
        imgContainer = metaBox.find('.twp-img-container'),
        imgIdInput = metaBox.find('.twp-img-id'),
        frame_title = metaBox.find('input[name=frame_title]').val(),
        button_text = metaBox.find('input[name=button_text]').val(),
        // File variables
        addFileLink = metaBox.find('.upload-custom-file'),
        delFileLink = metaBox.find('.delete-custom-file'),
        fileContainer = metaBox.find('.twp-file-container'),
        fileDetailsContainer = metaBox.find('#twp-file-details'),
        fileIconContainer = metaBox.find('#twp-file-icon'),
        fileIdInput = metaBox.find('.twp-file-id'),
        frame_title_file = metaBox.find('input[name=frame_title_file]').val(),
        button_text_file = metaBox.find('input[name=button_text_file]').val();
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
            title: frame_title,
            button: {
                text: button_text
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

    // ADD File LINK
    addFileLink.on('click', function (event) {
        event.preventDefault();
        // If the media frame already exists, reopen it.
        if (file_frame) {
            file_frame.open();
            return;
        }
        // Create a new media frame
        file_frame = wp.media({
            title: frame_title_file,
            button: {
                text: button_text_file
            },
            library: {
            },
            multiple: false // Set to true to allow multiple files to be selected
        });
        // When an file is selected in the media frame...
        file_frame.on('select', function () {
            // Get media attachment details from the frame state
            var attachment = file_frame.state().get('selection').first().toJSON();
            // Send the attachment URL to our custom file input field.
            console.log(attachment);
            var a = attachment;
            if (a.filesizeInBytes > 25000000){
                var warning = '<p style="color:#FFA700">Warning! The file size is larger than <strong> 25 MB </strong>. It may cause unexpected errors. </p>';
            } else if(a.filesizeInBytes > 50000000){
                var warning = '<p style="color:#FF0000">Error! The file size is larger than <strong> 50 MB </strong>. Telegram Bots can currently send files of up to 50 MB. Please select a smaller file.</p>';
            } else {
                var warning = '';
            }
            fileContainer.append('<div id="twp-file-icon"><a href="'+a.url+'" target="_blank" title="'+a.filename+'"><img src="' + a.icon + '" alt="'+a.filename+'" style="max-width:100%;"/></a></div><div id="twp-file-details"><p>Title: <span>'+a.filename+'</span></p><p>Caption: <span>'+a.caption+'</span></p><p>Size: <span>'+a.filesizeHumanReadable+'</span></p>'+warning+'</div>');

            // Send the attachment id to our hidden input
            fileIdInput.val(attachment.id);

            // Hide the add file link
            addFileLink.addClass('hidden');

            // Unhide the remove file link
            delFileLink.removeClass('hidden');
        });

        // Finally, open the modal on click
        file_frame.open();
    });
    // DELETE FILE LINK
    delFileLink.on('click', function (event) {

        event.preventDefault();

        // Clear out the preview file
        fileDetailsContainer.html('');
        fileIconContainer.html('');

        // Un-hide the add file link
        addFileLink.removeClass('hidden');

        // Hide the delete file link
        delFileLink.addClass('hidden');

        // Delete the file id from the hidden input
        fileIdInput.val('');

    });
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
