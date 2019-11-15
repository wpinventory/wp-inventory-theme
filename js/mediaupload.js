var index, formfield, tbframe_interval;
jQuery(document).ready(function() {
	jQuery('.upload_image_button').click(function() {
		 formfield = jQuery(this).attr('name');
		 index = jQuery(this).attr('name');
		 tb_show('', 'media-upload.php?post_id=0&#038;type=image&#038;TB_iframe=1');
 		 tbframe_interval = setInterval(function() {jQuery('#TB_iframeContent').contents().find('.savesend .button').val('Use This Image');}, 2000);
		 return false;
	});
	jQuery('.imageinput').click(function() {
		inputClick(this);
	});
	window.original_send_to_editor = window.send_to_editor;
	window.send_to_editor = function(html) {
		if (formfield) {
		 	imgurl = jQuery('img','<p>' + html + '</p>').attr('src');
			 jQuery('#' + index).val(imgurl);
			 tb_remove();
			 renderImage(index, imgurl);
		 // Clear the formfield
			formfield = "";
			// Clear the interval that changes the button name
			clearInterval(tbframe_interval);
		// If not, use the ORIGINAL
		} else {
			window.original_send_to_editor(html);
		}
	}
});

/* function to load the image url into the correct input box */
function renderImage(i, img) {
	id = i.substr(-1);
	jQuery("#preview_" + id).parents(".media").css("height", "auto");
	jQuery("#preview_" + id).html('<img src="' + img + '" alt="" /><a class="delete" title="Remove Image" href="javascript:void(0);" onclick="removeImage(' + id + ');">X</a>');
}

/* function assigned to the image input boxes to fire the "upload image" dialog */
function inputClick(el) {
	var id = jQuery(el).attr('id');
	id = id.substr(-1);
	jQuery('#image_button_' + id).click();
}

function removeImage(id) {
	jQuery("#preview_" + id).html('');
	jQuery('#image_' + id).val('');
	adjustMediaHeight();
}
