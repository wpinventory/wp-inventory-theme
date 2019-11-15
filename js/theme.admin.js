jQuery( function ( $ ) {
  // Script to attach to image upload and open wp media library window
  var orig_send_to_editor = window.send_to_editor;

  jQuery( '#upload_image_button' ).click( function () {
    formfield = jQuery( this ).prev( 'input' );
    tb_show( '', 'media-upload.php?type=image&amp;TB_iframe=true' );

    window.send_to_editor = function ( html ) {
      var regex  = /src="(.+?)"/;
      var rslt   = html.match( regex );
      var imgurl = rslt[ 1 ];
      formfield.val( imgurl );
      console.log(formfield.attr( 'name' ));
      tb_remove();
      jQuery( '#show_wpim_theme_logo' ).html( '<img src="' + imgurl + '" width="300">' )

      window.send_to_editor = orig_send_to_editor;
    }

    return false;
  } );

} );